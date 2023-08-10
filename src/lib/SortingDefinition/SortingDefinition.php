<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\SortingDefinition;

use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionInterface;

final class SortingDefinition implements SortingDefinitionInterface
{
    private string $identifier;

    private string $label;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] */
    private array $sortClauses;

    private int $priority;

    public function __construct(string $identifier, string $label, array $sortClauses, int $priority = 0)
    {
        $this->identifier = $identifier;
        $this->label = $label;
        $this->sortClauses = $sortClauses;
        $this->priority = $priority;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getSortClauses(): array
    {
        return $this->sortClauses;
    }
}
