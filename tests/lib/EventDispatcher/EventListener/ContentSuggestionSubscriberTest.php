<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\EventDispatcher\EventListener;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Search\Mapper\SearchHitToContentSuggestionMapper;
use Ibexa\Core\Repository\SiteAccessAware\SearchService;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Search\EventDispatcher\Event\ContentSuggestion;
use Ibexa\Search\EventDispatcher\EventListener\ContentSuggestionSubscriber;
use Ibexa\Search\Model\Suggestion\ContentSuggestion as ContentSuggestionModel;
use Ibexa\Search\Model\SuggestionQuery;
use PHPUnit\Framework\TestCase;

final class ContentSuggestionSubscriberTest extends TestCase
{
    public function testSubscribedEvents(): void
    {
        $this->assertSame(
            [ContentSuggestion::class => 'onContentSuggestion'],
            ContentSuggestionSubscriber::getSubscribedEvents()
        );
    }

    public function testOnContentSuggestion(): void
    {
        $query = new SuggestionQuery('test', 10, 'eng-GB');
        $searchService = $this->getSearchServiceMock();
        $locationService = $this->getLocationServiceMock();
        $mapper = $this->getSearchHitToContentSuggestionMapperMock();

        $subscriber = new ContentSuggestionSubscriber($searchService, $locationService, $mapper);

        $event = new ContentSuggestion($query);
        $subscriber->onContentSuggestion($event);

        $collection = $event->getSuggestionCollection();

        self::assertCount(1, $collection);
    }

    private function getSearchServiceMock(): SearchService
    {
        $searchServiceMock = $this->createMock(SearchService::class);
        $searchServiceMock->method('findContent')->willReturn(
            new SearchResult(
                [
                    'searchHits' => [
                        $this->createMock(SearchHit::class),
                    ],
                ]
            )
        );

        return $searchServiceMock;
    }

    private function getLocationServiceMock(): LocationService
    {
        $locationServiceMock = $this->createMock(LocationService::class);
        $locationServiceMock->method('loadLocation')->willReturn(
            $this->createMock(Location::class)
        );

        return $locationServiceMock;
    }

    private function getSearchHitToContentSuggestionMapperMock(): SearchHitToContentSuggestionMapper
    {
        $searchHitToContentSuggestionMapperMock = $this->createMock(SearchHitToContentSuggestionMapper::class);
        $searchHitToContentSuggestionMapperMock->method('map')->willReturn(
            new ContentSuggestionModel(10.0, 'test', 'test', 1, 'test', [0 => 'test'])
        );

        return $searchHitToContentSuggestionMapperMock;
    }
}
