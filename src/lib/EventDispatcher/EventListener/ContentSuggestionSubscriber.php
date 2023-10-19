<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\EventDispatcher\EventListener;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\FullText;
use Ibexa\Core\Repository\SiteAccessAware\SearchService;
use Ibexa\Search\EventDispatcher\Event\ContentSuggestion;
use Ibexa\Search\Mapper\SearchHitToContentSuggestionMapper;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ContentSuggestionSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private SearchService $searchService;

    private SearchHitToContentSuggestionMapper $contentSuggestionMapper;

    public function __construct(
        SearchService $searchService,
        SearchHitToContentSuggestionMapper $contentSuggestionMapper
    ) {
        $this->searchService = $searchService;
        $this->contentSuggestionMapper = $contentSuggestionMapper;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentSuggestion::class => 'onContentSuggestion',
        ];
    }

    public function onContentSuggestion(ContentSuggestion $event): ContentSuggestion
    {
        $query = $event->getQuery();

        $value = $query->getQuery();
        $limit = $query->getLimit();
        $language = $query->getLanguage();

        $criterion = new FullText($value);
        $query = new Query(['filter' => $criterion, 'limit' => $limit]);

        try {
            $languageFilter = $language ? ['languages' => [$language]] : [];
            $searchResult = $this->searchService->findContent($query, $languageFilter);
            $collection = $event->getSuggestionCollection();
            foreach ($searchResult as $result) {
                $mappedResult = $this->contentSuggestionMapper->map($result);
                $collection->append($mappedResult);
            }
        } catch (InvalidArgumentException $e) {
            $this->logger ? $this->logger->error($e) : null;
        }

        return $event;
    }
}
