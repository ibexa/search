<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\SortingDefinition;

interface SortingDefinitionRegistryInterface
{
    public function getDefaultSortingDefinition(): ?SortingDefinitionInterface;

    /**
     * @return \Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionInterface[]
     */
    public function getSortingDefinitions(): array;
}
