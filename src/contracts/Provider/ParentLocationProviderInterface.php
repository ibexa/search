<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\Provider;

interface ParentLocationProviderInterface
{
    /**
     * @param array<int> $parentLocationIds
     *
     * @return array<\Ibexa\Contracts\Core\Repository\Values\Content\Location>
     */
    public function provide(array $parentLocationIds): array;
}
