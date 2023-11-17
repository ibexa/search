<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\SortingDefinition\Provider;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\DateModified;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Score;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinition;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RelevanceSortingDefinitionProvider implements SortingDefinitionProviderInterface
{
    private SearchService $searchService;

    private TranslatorInterface $translator;

    public function __construct(SearchService $searchService, TranslatorInterface $translator)
    {
        $this->searchService = $searchService;
        $this->translator = $translator;
    }

    public function getSortingDefinitions(): array
    {
        return [
            new SortingDefinition(
                'relevance',
                $this->getLabel(),
                $this->getSortClauses(),
                100
            ),
        ];
    }

    private function getLabel(): string
    {
        return $this->translator->trans(
            /** @Desc("Sort by relevance") */
            'sort_definition.relevance.label',
            [],
            'ibexa_search'
        );
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[]
     */
    public function getSortClauses(): array
    {
        if ($this->searchService->supports(SearchService::CAPABILITY_SCORING)) {
            return [
                new Score(Query::SORT_DESC),
            ];
        }

        return [
            new DateModified(Query::SORT_DESC),
        ];
    }
}
