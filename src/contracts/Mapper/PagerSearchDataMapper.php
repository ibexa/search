<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\Mapper;

use Pagerfanta\Pagerfanta;

/**
 * @template TData
 */
interface PagerSearchDataMapper
{
    /**
     * @param Pagerfanta<\Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit<\Ibexa\Contracts\Core\Repository\Values\Content\Content>> $pager
     *
     * @phpstan-return TData[]
     */
    public function map(Pagerfanta $pager): array;
}
