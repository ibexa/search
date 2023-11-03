<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\EventDispatcher\EventListener;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Search\Event\BuildSuggestionCollectionEvent;
use Ibexa\Contracts\Search\Mapper\SearchHitToContentSuggestionMapperInterface;
use Ibexa\Contracts\Search\Model\Suggestion\ContentSuggestion as ContentSuggestionModel;
use Ibexa\Contracts\Search\Model\Suggestion\ParentLocation;
use Ibexa\Core\Repository\SiteAccessAware\SearchService;
use Ibexa\Search\EventDispatcher\EventListener\ContentSuggestionSubscriber;
use Ibexa\Search\Model\SuggestionQuery;
use PHPUnit\Framework\TestCase;

final class ContentSuggestionSubscriberTest extends TestCase
{
    public function testSubscribedEvents(): void
    {
        $this->assertSame(
            [BuildSuggestionCollectionEvent::class => 'onBuildSuggestionCollectionEvent'],
            ContentSuggestionSubscriber::getSubscribedEvents()
        );
    }

    public function testOnContentSuggestion(): void
    {
        $query = new SuggestionQuery('test', 10, 'eng-GB');
        $searchService = $this->getSearchServiceMock();
        $mapper = $this->getSearchHitToContentSuggestionMapperMock();

        $subscriber = new ContentSuggestionSubscriber($searchService, $mapper);

        $event = new BuildSuggestionCollectionEvent($query);
        $subscriber->onBuildSuggestionCollectionEvent($event);

        $collection = $event->getSuggestionCollection();

        self::assertCount(1, $collection);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Core\Repository\SiteAccessAware\SearchService
     */
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

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Contracts\Search\Mapper\SearchHitToContentSuggestionMapperInterface
     */
    private function getSearchHitToContentSuggestionMapperMock(): SearchHitToContentSuggestionMapperInterface
    {
        $searchHitToContentSuggestionMapperMock = $this->createMock(SearchHitToContentSuggestionMapperInterface::class);
        $searchHitToContentSuggestionMapperMock->method('map')->willReturn(
            new ContentSuggestionModel(
                10.0,
                $this->createMock(ContentType::class),
                'test',
                1,
                2,
                'test',
                [
                    new ParentLocation(1, 2, 'test'),
                ]
            )
        );

        return $searchHitToContentSuggestionMapperMock;
    }
}
