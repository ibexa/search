<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Form\ChoiceList\View;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;

final class FacetView extends ChoiceView
{
    public ?TermAggregationResultEntry $term = null;

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

        return $facet;
    }
}
