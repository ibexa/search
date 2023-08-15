<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Form\ChoiceList\View;

use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;

final class FacetGroupView extends ChoiceGroupView
{
    public static function createFromChoiceGroupView(ChoiceGroupView $groupView): self
    {
        return new self(
            $groupView->label,
            $groupView->choices
        );
    }
}
