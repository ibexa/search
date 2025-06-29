<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Form\ChoiceList\View;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;

/**
 * @phpstan-template TKey of object|scalar
 */
final class FacetView extends ChoiceView
{
    /**
     * @phpstan-var \Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry<TKey>|null
     */
    public ?TermAggregationResultEntry $term = null;

    /**
     * @phpstan-param \Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry<TKey>|null $term
     *
     * @phpstan-return \Ibexa\Bundle\Search\Form\ChoiceList\View\FacetView<TKey>
     */
    public static function createFromChoiceView(ChoiceView $choiceView, ?TermAggregationResultEntry $term): self
    {
        $facet = new self(
            $choiceView->data,
            $choiceView->value,
            $choiceView->label,
            $choiceView->attr,
            $choiceView->labelTranslationParameters,
        );

        $facet->term = $term;

        /** @phpstan-var \Ibexa\Bundle\Search\Form\ChoiceList\View\FacetView<TKey> */
        return $facet;
    }
}
