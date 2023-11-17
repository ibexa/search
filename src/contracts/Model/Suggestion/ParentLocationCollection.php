<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\Model\Suggestion;

use Ibexa\Contracts\Core\Collection\MutableArrayList;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

/**
 * @template-extends \Ibexa\Contracts\Core\Collection\MutableArrayList<
 *     \Ibexa\Contracts\Core\Repository\Values\Content\Location
 * >
 */
final class ParentLocationCollection extends MutableArrayList
{
    /**
     * @param mixed $item
     */
    public function append($item): void
    {
        if (!$item instanceof Location) {
            throw new InvalidArgumentException(
                '$item',
                sprintf(
                    'Argument 1 passed to %s() must be an instance of %s, %s given',
                    __METHOD__,
                    Location::class,
                    get_debug_type($item),
                )
            );
        }

        parent::append($item);
    }
}
