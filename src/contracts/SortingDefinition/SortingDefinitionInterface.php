<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\SortingDefinition;

interface SortingDefinitionInterface
{
    public function getIdentifier(): string;

    public function getLabel(): string;

    public function getPriority(): int;

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[]
     */
    public function getSortClauses(): array;
}
