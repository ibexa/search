<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\EventDispatcher\EventListener;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Search\Event\BuildSuggestionCollectionEvent;
use Ibexa\Contracts\Search\Mapper\SearchHitToContentSuggestionMapperInterface;
use Ibexa\Core\Repository\SiteAccessAware\SearchService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ContentSuggestionSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private SearchService $searchService;

    private SearchHitToContentSuggestionMapperInterface $contentSuggestionMapper;

    public function __construct(
        SearchService $searchService,
        SearchHitToContentSuggestionMapperInterface $contentSuggestionMapper
    ) {
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

        $query = new Query(
            [
                'query' => new Query\Criterion\FullText($value . '*'),
                'limit' => $limit,
            ]
        );

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
}
