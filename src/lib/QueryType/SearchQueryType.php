<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\QueryType;

use Ibexa\Bundle\Search\Form\Data\SearchData;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\ContentTypeTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\SectionTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentId;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Spellcheck;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionRegistryInterface;
use Ibexa\Core\QueryType\OptionsResolverBasedQueryType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchQueryType extends OptionsResolverBasedQueryType
{
    private SearchService $searchService;

    private SortingDefinitionRegistryInterface $sortingDefinitionRegistry;

    public function __construct(
        SearchService $searchService,
        SortingDefinitionRegistryInterface $sortingDefinitionRegistry
    ) {
        $this->sortingDefinitionRegistry = $sortingDefinitionRegistry;
        $this->searchService = $searchService;
    }

    protected function doGetQuery(array $parameters): Query
    {
        /** @var \Ibexa\Bundle\Search\Form\Data\SearchData $searchData */
        $searchData = $parameters['search_data'];

        $query = new Query();
        if (null !== $searchData->getQuery()) {
            $query->query = new Criterion\FullText($searchData->getQuery());

            if ($this->searchService->supports(SearchService::CAPABILITY_SPELLCHECK)) {
                $query->spellcheck = new Spellcheck($searchData->getQuery());
            }
        }

        $criteria = $this->buildCriteria($searchData);
        if (!empty($criteria)) {
            $query->filter = new Criterion\LogicalAnd($criteria);
        }

        $sortingDefinition = $searchData->getSortingDefinition() ?? $this->sortingDefinitionRegistry->getDefaultSortingDefinition();
        if ($sortingDefinition !== null) {
            $query->sortClauses = $sortingDefinition->getSortClauses();
        }

        // Search results order MUST BE deterministic
        $query->sortClauses[] = new ContentId(Query::SORT_ASC);

        if ($this->searchService->supports(SearchService::CAPABILITY_AGGREGATIONS)) {
            $query->aggregations[] = $this->buildContentTypeTermAggregation($parameters);
            $query->aggregations[] = $this->buildSectionTermAggregation($parameters);
        }

        return $query;
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'search_data' => new SearchData(),
            'facets_limit' => 128,
        ]);

        $optionsResolver->setAllowedTypes('search_data', SearchData::class);
    }

    public static function getName(): string
    {
        return 'PlatformSearch:SearchQuery';
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion[]
     */
    protected function buildCriteria(SearchData $searchData): array
    {
        $criteria = [];

        if (null !== $searchData->getSection()) {
            $criteria[] = new Criterion\SectionId($searchData->getSection()->id);
        }

        if (!empty($searchData->getContentTypes())) {
            $criteria[] = new Criterion\ContentTypeId(array_column($searchData->getContentTypes(), 'id'));
        }

        if (!empty($searchData->getLastModified())) {
            $modified = $searchData->getLastModified();

            $criteria[] = new Criterion\DateMetadata(
                Criterion\DateMetadata::MODIFIED,
                Criterion\Operator::BETWEEN,
                [
                    $modified['start_date'],
                    $modified['end_date'],
                ]
            );
        }

        if (!empty($searchData->getCreated())) {
            $created = $searchData->getCreated();

            $criteria[] = new Criterion\DateMetadata(
                Criterion\DateMetadata::CREATED,
                Criterion\Operator::BETWEEN,
                [
                    $created['start_date'],
                    $created['end_date'],
                ]
            );
        }

        if ($searchData->getCreator() instanceof User) {
            $criteria[] = new Criterion\UserMetadata(
                Criterion\UserMetadata::OWNER,
                Criterion\Operator::EQ,
                $searchData->getCreator()->id
            );
        }

        if (null !== $searchData->getSearchUsersData() && null !== $searchData->getSearchUsersData()->getQuery()) {
            if (!empty($searchData->getSearchUsersData()->getPossibleUsers())) {
                $criteria[] = new Criterion\UserMetadata(
                    Criterion\UserMetadata::OWNER,
                    Criterion\Operator::IN,
                    array_column($searchData->getSearchUsersData()->getPossibleUsers(), 'id')
                );
            } else {
                // If no users matched user query, do not return any content.
                $criteria[] = new Criterion\MatchNone();
            }
        }

        if (null !== $searchData->getSubtree()) {
            $criteria[] = new Criterion\Subtree($searchData->getSubtree());
        }

        return $criteria;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function buildContentTypeTermAggregation(array $parameters): ContentTypeTermAggregation
    {
        $aggregation = new ContentTypeTermAggregation('content_types');
        $aggregation->setLimit($parameters['facets_limit']);

        return $aggregation;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function buildSectionTermAggregation(array $parameters): SectionTermAggregation
    {
        $aggregation = new SectionTermAggregation('sections');
        $aggregation->setLimit($parameters['facets_limit']);

        return $aggregation;
    }
}

class_alias(SearchQueryType::class, 'Ibexa\Platform\Search\QueryType\SearchQueryType');
