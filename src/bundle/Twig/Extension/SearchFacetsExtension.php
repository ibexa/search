<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Twig\Extension;

use Ibexa\Bundle\Search\Form\ChoiceList\View\FacetGroupView;
use Ibexa\Bundle\Search\Form\ChoiceList\View\FacetView;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry;
use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class SearchFacetsExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'ibexa_choices_as_facets',
                [$this, 'getChoicesAsFacets']
            ),
        ];
    }

    /**
     * @param \Symfony\Component\Form\ChoiceList\View\ChoiceView[]|\Symfony\Component\Form\ChoiceList\View\ChoiceGroupView[] $choices
     * @param callable(ChoiceView, TermAggregationResultEntry): bool|null $comparator
     *
     * @return \Symfony\Component\Form\ChoiceList\View\ChoiceView[]|\Symfony\Component\Form\ChoiceList\View\ChoiceGroupView[]
     */
    public function getChoicesAsFacets(
        array $choices,
        ?TermAggregationResult $terms,
        ?callable $comparator = null
    ): array {
        if ($terms === null) {
            return $choices;
        }

        if ($comparator === null) {
            $comparator = static function (ChoiceView $choice, TermAggregationResultEntry $term): bool {
                return $choice->data == $term->getKey();
            };
        }

        $facets = [];
        foreach ($choices as $key => $choice) {
            if ($choice instanceof ChoiceGroupView) {
                $group = FacetGroupView::createFromChoiceGroupView($choice);
                $group->choices = $this->getChoicesAsFacets($group->choices, $terms, $comparator);
                if (!empty($group->choices)) {
                    $facets[$key] = $group;
                }

                continue;
            }

            $term = $this->findTermEntry($terms, $choice, $comparator);
            if ($term !== null) {
                $facet = FacetView::createFromChoiceView($choice, $term);
                $facet->label = sprintf('%s (%d)', $choice->label, $term->getCount());
                $facets[$key] = $facet;
            }
        }

        return $facets;
    }

    /**
     * @param callable(ChoiceView, TermAggregationResultEntry): bool $comparator
     */
    private function findTermEntry(
        TermAggregationResult $terms,
        ChoiceView $choice,
        callable $comparator
    ): ?TermAggregationResultEntry {
        foreach ($terms->getEntries() as $entry) {
            if ($comparator($choice, $entry) === true) {
                return $entry;
            }
        }

        return null;
    }
}
