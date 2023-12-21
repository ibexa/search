<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\EventDispatcher\EventListener;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentName;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentTranslatedName;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\DateModified;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Score;
use Ibexa\Contracts\Search\Event\BuildSuggestionCollectionEvent;
use Ibexa\Contracts\Search\Mapper\SearchHitToContentSuggestionMapperInterface;
use Ibexa\Core\Repository\SiteAccessAware\SearchService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ContentSuggestionSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private RepositoryConfigurationProvider $configurationProvider;

    private SearchService $searchService;

    private SearchHitToContentSuggestionMapperInterface $contentSuggestionMapper;

    public function __construct(
        RepositoryConfigurationProvider $configurationProvider,
        SearchService $searchService,
        SearchHitToContentSuggestionMapperInterface $contentSuggestionMapper
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->searchService = $searchService;
        $this->contentSuggestionMapper = $contentSuggestionMapper;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BuildSuggestionCollectionEvent::class => 'onBuildSuggestionCollectionEvent',
        ];
    }

    public function onBuildSuggestionCollectionEvent(
        BuildSuggestionCollectionEvent $event
    ): BuildSuggestionCollectionEvent {
        $query = $event->getQuery();

        $value = $query->getQuery();
        $limit = $query->getLimit();
        $language = $query->getLanguageCode();

        $query = $this->getQuery($value, $limit);

        try {
            $languageFilter = $language ? ['languages' => [$language]] : [];
            $searchResult = $this->searchService->findContent($query, $languageFilter);
            $suggestionCollection = $event->getSuggestionCollection();
            foreach ($searchResult as $result) {
                $contentSuggestion = $this->contentSuggestionMapper->map($result);
                if ($contentSuggestion === null) {
                    continue;
                }
                $suggestionCollection->append($contentSuggestion);
            }

            $suggestionCollection->increaseTotalCount($searchResult->totalCount ?? 0);
        } catch (InvalidArgumentException $e) {
            $this->logger->error($e);
        }

        return $event;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[]
     */
    private function getSortClauses(): array
    {
        $sortClauses = [];

        if ($this->isLegacySearchEngine()) {
            $sortClauses[] = new ContentName(Query::SORT_DESC);
        } else {
            $sortClauses[] = new ContentTranslatedName(Query::SORT_DESC);
        }

        if ($this->searchService->supports(SearchService::CAPABILITY_SCORING)) {
            $sortClauses[] = new Score(Query::SORT_DESC);
        } else {
            $sortClauses[] = new DateModified(Query::SORT_DESC);
        }

        return $sortClauses;
    }

    private function isLegacySearchEngine(): bool
    {
        $config = $this->configurationProvider->getRepositoryConfig();

        return $config['search']['engine'] === 'legacy';
    }

    private function getQuery(string $value, int $limit): Query
    {
        $query = new Query();
        $query->query = new Query\Criterion\FullText($value . '*');
        $query->limit = $limit;
        $query->sortClauses = $this->getSortClauses();

        return $query;
    }
}
