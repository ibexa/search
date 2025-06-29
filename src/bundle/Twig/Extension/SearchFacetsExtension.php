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
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class SearchFacetsExtension extends AbstractExtension
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'ibexa_choices_as_facets',
                $this->getChoicesAsFacets(...)
            ),
        ];
    }

    /**
     * @phpstan-template TKey of object|scalar
     *
     * @phpstan-param \Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResult<TKey>|null $terms
     * @phpstan-param \Symfony\Component\Form\ChoiceList\View\ChoiceView[]|\Symfony\Component\Form\ChoiceList\View\ChoiceGroupView[] $choices
     * @phpstan-param callable(ChoiceView, TermAggregationResultEntry<TKey>): bool|null $comparator
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
                return $choice->data === $term->getKey();
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
                $label = $choice->label;
                if ($label instanceof TranslatableInterface) {
                    $label = $label->trans($this->translator);
                }

                $facet = FacetView::createFromChoiceView($choice, $term);
                $facet->label = sprintf('%s (%d)', $label, $term->getCount());
                $facets[$key] = $facet;
            }
        }

        return $facets;
    }

    /**
     * @phpstan-template TKey of object|scalar
     *
     * @phpstan-param \Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResult<TKey> $terms
     * @phpstan-param callable(ChoiceView, TermAggregationResultEntry<TKey>): bool $comparator
     *
     * @phpstan-return \Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry<TKey>|null
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
