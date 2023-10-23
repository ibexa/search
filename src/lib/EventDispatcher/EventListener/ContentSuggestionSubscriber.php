<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\EventDispatcher\EventListener;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Search\Mapper\SearchHitToContentSuggestionMapper;
use Ibexa\Core\Repository\SiteAccessAware\SearchService;
use Ibexa\Search\EventDispatcher\Event\ContentSuggestion;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ContentSuggestionSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private SearchService $searchService;

    private SearchHitToContentSuggestionMapper $contentSuggestionMapper;

    private LocationService $locationService;

    public function __construct(
        SearchService $searchService,
        LocationService $locationService,
        SearchHitToContentSuggestionMapper $contentSuggestionMapper
    ) {
        $this->searchService = $searchService;
        $this->contentSuggestionMapper = $contentSuggestionMapper;
        $this->locationService = $locationService;
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

        $query = new Query(
            [
                'query' => new Query\Criterion\FullText($value),
                'limit' => $limit,
            ]
        );

        try {
            $languageFilter = $language ? ['languages' => [$language]] : [];
            $searchResult = $this->searchService->findContent($query, $languageFilter);
            $collection = $event->getSuggestionCollection();
            foreach ($searchResult as $result) {
                $mappedResult = $this->contentSuggestionMapper->map($result);
                if ($mappedResult === null) {
                    continue;
                }

                foreach ($mappedResult->getParentsLocation() as $locationId => $name) {
                    try {
                        $location = $this->locationService->loadLocation($locationId);
                        $mappedResult->addPath($locationId, (string) $location->getContent()->getName());
                    } catch (NotFoundException $e) {
                    } catch (UnauthorizedException $e) {
                    }
                }
                $collection->append($mappedResult);
            }
        } catch (InvalidArgumentException $e) {
            $this->logger ? $this->logger->error($e) : null;
        }

        return $event;
    }
}
