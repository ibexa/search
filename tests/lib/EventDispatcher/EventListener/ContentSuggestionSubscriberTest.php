<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\EventDispatcher\EventListener;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\SearchService as SearchServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentName;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentTranslatedName;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Search\Event\BuildSuggestionCollectionEvent;
use Ibexa\Contracts\Search\Mapper\SearchHitToContentSuggestionMapperInterface;
use Ibexa\Contracts\Search\Model\Suggestion\ContentSuggestion as ContentSuggestionModel;
use Ibexa\Contracts\Search\Model\Suggestion\Suggestion;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionInterface;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionRegistryInterface;
use Ibexa\Core\Repository\SiteAccessAware\SearchService;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Search\EventDispatcher\EventListener\ContentSuggestionSubscriber;
use Ibexa\Search\Model\SuggestionQuery;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ContentSuggestionSubscriberTest extends TestCase
{
    /** @var \Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider&\PHPUnit\Framework\MockObject\MockObject */
    private RepositoryConfigurationProvider $configProviderMock;

    private ?Query $capturedQuery;

    private bool $searchServiceSupportsScoring = false;

    /** @var \Psr\Log\LoggerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private LoggerInterface $loggerMock;

    protected function setUp(): void
    {
        $this->configProviderMock = $this->createMock(RepositoryConfigurationProvider::class);
        $this->capturedQuery = null;
        $this->searchServiceSupportsScoring = false;
        $this->loggerMock = $this->createMock(LoggerInterface::class);
    }

    public function testSubscribedEvents(): void
    {
        self::assertSame(
            [
                BuildSuggestionCollectionEvent::class => 'onBuildSuggestionCollectionEvent',
            ],
            ContentSuggestionSubscriber::getSubscribedEvents()
        );
    }

    public function testOnContentSuggestionWithLegacyEngineAndScoring(): void
    {
        $this->configProviderMock
            ->method('getRepositoryConfig')
            ->willReturn(['search' => ['engine' => 'legacy']]);
        $this->searchServiceSupportsScoring = true;
        $this->performTestOnContentSuggestion();
    }

    public function testOnContentSuggestionWithNonLegacyEngineAndScoring(): void
    {
        $this->configProviderMock
            ->method('getRepositoryConfig')
            ->willReturn(['search' => ['engine' => 'not_legacy']]);
        $this->searchServiceSupportsScoring = true;
        $this->performTestOnContentSuggestion();
    }

    public function testOnContentSuggestionWithLegacyEngineWithoutScoring(): void
    {
        $this->configProviderMock
            ->method('getRepositoryConfig')
            ->willReturn(['search' => ['engine' => 'legacy']]);
        $this->searchServiceSupportsScoring = false;
        $this->performTestOnContentSuggestion();
    }

    public function testOnContentSuggestionWithNonLegacyEngineWithoutScoring(): void
    {
        $this->configProviderMock
            ->method('getRepositoryConfig')
            ->willReturn(['search' => ['engine' => 'not_legacy']]);
        $this->searchServiceSupportsScoring = false;
        $this->performTestOnContentSuggestion();
    }

    public function testOnContentSuggestionWithException(): void
    {
        $this->configProviderMock
            ->method('getRepositoryConfig')
            ->willReturn(['search' => ['engine' => 'legacy']]);
        $this->searchServiceSupportsScoring = true;

        $query = new SuggestionQuery('test', 10, 'eng-GB');

        $searchService = $this->getSearchServiceMockWithException();
        $mapper = $this->getSearchHitToContentSuggestionMapperMock();
        $sortingDefinitionRegistry = $this->getSortingDefinitionRegistryMock();

        $subscriber = new ContentSuggestionSubscriber($searchService, $mapper, $sortingDefinitionRegistry);
        $subscriber->setLogger($this->loggerMock);

        $event = new BuildSuggestionCollectionEvent($query);

        $this->loggerMock
            ->expects($this->once())
            ->method('error');

        $subscriber->onBuildSuggestionCollectionEvent($event);
    }

    private function performTestOnContentSuggestion(): void
    {
        $query = new SuggestionQuery('test', 10, 'eng-GB');
        $searchService = $this->getSearchServiceMock();
        $mapper = $this->getSearchHitToContentSuggestionMapperMock();

        $sortingDefinitionRegistry = $this->getSortingDefinitionRegistryMock();

        $subscriber = new ContentSuggestionSubscriber($searchService, $mapper, $sortingDefinitionRegistry);

        $event = new BuildSuggestionCollectionEvent($query);
        $subscriber->onBuildSuggestionCollectionEvent($event);

        $collection = $event->getSuggestionCollection();

        self::assertCount(1, $collection);

        self::assertNotNull($this->capturedQuery);
        self::assertNotEmpty($this->capturedQuery->sortClauses);
    }

    private function getSearchServiceMock(): SearchService
    {
        $searchServiceMock = $this->createMock(SearchService::class);
        $searchServiceMock
            ->method('supports')
            ->willReturnCallback(function ($capability): bool {
                return $capability === SearchServiceInterface::CAPABILITY_SCORING && $this->searchServiceSupportsScoring;
            });
        $searchServiceMock
            ->method('findContent')->willReturnCallback(function (Query $query): SearchResult {
                $this->capturedQuery = $query;

                return new SearchResult(['searchHits' => [$this->createMock(SearchHit::class)]]);
            });

        return $searchServiceMock;
    }

    private function getSearchServiceMockWithException(): SearchService
    {
        $searchServiceMock = $this->createMock(SearchService::class);
        $searchServiceMock
            ->method('findContent')
            ->willThrowException(
                new InvalidArgumentException(
                    '$item',
                    sprintf(
                        'Argument 1 passed to %s() must be an instance of %s, %s given',
                        'SuggestionCollection::append',
                        Suggestion::class,
                        get_debug_type('type'),
                    )
                )
            );

        return $searchServiceMock;
    }

    private function getSearchHitToContentSuggestionMapperMock(): SearchHitToContentSuggestionMapperInterface
    {
        $searchHitToContentSuggestionMapperMock = $this->createMock(
            SearchHitToContentSuggestionMapperInterface::class
        );
        $searchHitToContentSuggestionMapperMock
            ->method('map')
            ->willReturn(
                new ContentSuggestionModel(
                    10.0,
                    $this->createMock(Content::class),
                    $this->createMock(ContentType::class),
                    '1/2/3',
                    [
                        new Location(['id' => 1, 'path' => [1, 2, 3]]),
                    ]
                )
            );

        return $searchHitToContentSuggestionMapperMock;
    }

    private function getSortingDefinitionRegistryMock(): SortingDefinitionRegistryInterface
    {
        $sortingDefinitionRegistryMock = $this->createMock(SortingDefinitionRegistryInterface::class);

        $sortingDefinitionRegistryMock
            ->method('getDefaultSortingDefinition')
            ->willReturn($this->getSortingDefinitionMock());

        return $sortingDefinitionRegistryMock;
    }

    private function getSortingDefinitionMock(): SortingDefinitionInterface
    {
        $sortingDefinitionMock = $this->createMock(SortingDefinitionInterface::class);
        $sortingDefinitionMock
            ->method('getSortClauses')
            ->willReturn($this->getSortClauses());

        return $sortingDefinitionMock;
    }

    /**
     * @return array<\Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause>
     */
    private function getSortClauses(): array
    {
        return [
            new ContentTranslatedName(Query::SORT_ASC),
            new ContentName(Query::SORT_DESC),
        ];
    }
}
