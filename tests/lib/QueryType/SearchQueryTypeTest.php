<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\QueryType;

use Ibexa\Bundle\Search\Form\Data\SearchData;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionRegistryInterface;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Search\QueryType\SearchQueryType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class SearchQueryTypeTest extends TestCase
{
    private const string EXPECTED_QUERY_STRING = 'Ibexa';
    private const int EXPECTED_SECTION_ID = 2;
    private const array EXPECTED_CONTENT_TYPE_IDS = [3, 5, 7];
    private const int EXPECTED_USER_ID = 11;
    private const string EXPECTED_SUBTREE = '/13/17/19/';
    private const array EXPECTED_DATE_RANGE = [1431993600, 1587340800];

    private SearchService&MockObject $searchService;

    private SortingDefinitionRegistryInterface&Stub $sortingDefinitionRegistry;

    private SearchQueryType $queryType;

    protected function setUp(): void
    {
        $this->searchService = $this->createMock(SearchService::class);
        $this->sortingDefinitionRegistry = self::createStub(SortingDefinitionRegistryInterface::class);
        $this->queryType = new SearchQueryType(
            $this->searchService,
            $this->sortingDefinitionRegistry
        );
    }

    /**
     * @param array{searchData: \Ibexa\Bundle\Search\Form\Data\SearchData} $parameters
     * @param array<int, array<int, mixed>> $returnMap
     */
    #[DataProvider('dataProviderForGetQuery')]
    public function testGetQuery(
        array $parameters,
        Query $expectedQuery,
        array $returnMap
    ): void {
        $this->searchService
            ->method('supports')
            ->willReturnMap($returnMap);

        self::assertEquals($expectedQuery, $this->queryType->getQuery($parameters));
    }

    /**
     * @return iterable<array{
     *     array{searchData?: \Ibexa\Bundle\Search\Form\Data\SearchData},
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Query,
     *     array<int, array<int, mixed>>
     * }>
     */
    public static function dataProviderForGetQuery(): iterable
    {
        $aggregations = self::createExpectedAggregations();

        return [
            [
                [],
                new Query([
                    'sortClauses' => [
                        new SortClause\ContentId(),
                    ],
                    'aggregations' => $aggregations,
                ]),
                [
                    [SearchService::CAPABILITY_AGGREGATIONS, true],
                ],
            ],
            [
                [
                    'search_data' => self::createSearchDataWithAllCriteria(),
                ],
                self::createExpectedQueryForAllCriteria(
                    [new SortClause\ContentId()],
                    $aggregations
                ),
                [
                    [SearchService::CAPABILITY_SPELLCHECK, false],
                    [SearchService::CAPABILITY_AGGREGATIONS, true],
                ],
            ],
            [
                [],
                new Query([
                    'sortClauses' => [
                        new SortClause\ContentId(),
                    ],
                ]),
                [
                    [SearchService::CAPABILITY_AGGREGATIONS, false],
                ],
            ],
            [
                [
                    'search_data' => self::createSearchDataWithAllCriteria(),
                ],
                self::createExpectedQueryForAllCriteria(),
                [
                    [SearchService::CAPABILITY_SPELLCHECK, false],
                    [SearchService::CAPABILITY_AGGREGATIONS, false],
                ],
            ],
        ];
    }

    /**
     * @param array<int> $ids
     *
     * @return array<\Ibexa\Core\Repository\Values\ContentType\ContentType>
     */
    private static function createContentTypesList(array $ids): array
    {
        return array_map(static function (int $id): ContentType {
            return new ContentType(['id' => $id]);
        }, $ids);
    }

    private static function createSearchDataWithAllCriteria(): SearchData
    {
        $searchData = new SearchData();
        $searchData->setQuery(self::EXPECTED_QUERY_STRING);
        $searchData->setSection(new Section(['id' => self::EXPECTED_SECTION_ID]));
        $searchData->setContentTypes(self::createContentTypesList(self::EXPECTED_CONTENT_TYPE_IDS));
        $searchData->setCreated([
            'start_date' => self::EXPECTED_DATE_RANGE[0],
            'end_date' => self::EXPECTED_DATE_RANGE[1],
        ]);
        $searchData->setLastModified([
            'start_date' => self::EXPECTED_DATE_RANGE[0],
            'end_date' => self::EXPECTED_DATE_RANGE[1],
        ]);
        $searchData->setCreator(self::createUser(self::EXPECTED_USER_ID));
        $searchData->setSubtree(self::EXPECTED_SUBTREE);

        return $searchData;
    }

    private static function createUser(int $id): User
    {
        $user = self::createStub(User::class);
        $user->method('__get')->willReturnCallback(
            static fn (string $property): ?int => $property === 'id' ? $id : null
        );

        return $user;
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause>|null $expectedSortClauses
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation> $expectedAggregations
     */
    private static function createExpectedQueryForAllCriteria(
        ?array $expectedSortClauses = null,
        array $expectedAggregations = []
    ): Query {
        if ($expectedSortClauses === null) {
            $expectedSortClauses = [
                new SortClause\ContentId(),
            ];
        }

        return new Query([
            'query' => new Criterion\FullText(self::EXPECTED_QUERY_STRING),
            'filter' => new Criterion\LogicalAnd([
                new Criterion\SectionId(self::EXPECTED_SECTION_ID),
                new Criterion\ContentTypeId(self::EXPECTED_CONTENT_TYPE_IDS),
                new Criterion\DateMetadata(
                    Criterion\DateMetadata::MODIFIED,
                    Criterion\Operator::BETWEEN,
                    self::EXPECTED_DATE_RANGE
                ),
                new Criterion\DateMetadata(
                    Criterion\DateMetadata::CREATED,
                    Criterion\Operator::BETWEEN,
                    self::EXPECTED_DATE_RANGE
                ),
                new Criterion\UserMetadata(
                    Criterion\UserMetadata::OWNER,
                    Criterion\Operator::EQ,
                    self::EXPECTED_USER_ID
                ),
                new Criterion\Subtree(self::EXPECTED_SUBTREE),
            ]),
            'sortClauses' => $expectedSortClauses,
            'aggregations' => $expectedAggregations,
        ]);
    }

    /**
     * @return array<\Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation>
     */
    private static function createExpectedAggregations(): array
    {
        $contentTypeTermAggregation = new Query\Aggregation\ContentTypeTermAggregation('content_types');
        $contentTypeTermAggregation->setLimit(SearchService::CAPABILITY_AGGREGATIONS);

        $sectionTermAggregation = new Query\Aggregation\SectionTermAggregation('sections');
        $sectionTermAggregation->setLimit(SearchService::CAPABILITY_AGGREGATIONS);

        return [
            $contentTypeTermAggregation,
            $sectionTermAggregation,
        ];
    }
}
