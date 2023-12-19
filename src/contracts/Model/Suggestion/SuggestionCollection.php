<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\Model\Suggestion;

use Ibexa\Contracts\Core\Collection\MutableArrayList;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;

/**
 * @template-extends \Ibexa\Contracts\Core\Collection\MutableArrayList<
 *     \Ibexa\Contracts\Search\Model\Suggestion\Suggestion
 * >
 */
final class SuggestionCollection extends MutableArrayList
{
    private int $totalCount;

    public function __construct(array $items = [], int $totalCount = 0)
    {
        parent::__construct($items);
        $this->items = $items;
        $this->totalCount = $totalCount;
    }

    /**
     * @param mixed $item
     */
    public function append($item): void
    {
        if (!$item instanceof Suggestion) {
            throw new InvalidArgumentException(
                '$item',
                sprintf(
                    'Argument 1 passed to %s() must be an instance of %s, %s given',
                    __METHOD__,
                    Suggestion::class,
                    get_debug_type($item),
                )
            );
        }

        parent::append($item);
    }

    public function sortByScore(): void
    {
        usort($this->items, static fn ($a, $b): int => $b->getScore() <=> $a->getScore());
    }

    public function truncate(int $count): void
    {
        $this->items = array_slice($this->items, 0, $count);
    }

    public function increaseTotalCount(int $totalCount): void
    {
        $this->totalCount += $totalCount;
    }

    public function decreaseTotalCount(int $totalCount): void
    {
        $this->totalCount -= $totalCount;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}
