<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\SortingDefinition;

/**
 * Default SortingDefinitionInterface implementation.
 */
final class SortingDefinition implements SortingDefinitionInterface
{
    private string $identifier;

    private string $label;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] */
    private array $sortClauses;

    private int $priority;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     */
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
