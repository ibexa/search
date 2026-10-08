<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\SortingDefinition;

use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionInterface;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionProviderInterface;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionRegistryInterface;

final class SortingDefinitionRegistry implements SortingDefinitionRegistryInterface
{
    /** @var iterable<SortingDefinitionProviderInterface> */
    private iterable $providers;

    /** @var SortingDefinitionInterface[] */
    private ?array $definitions = null;

    /**
     * @param iterable<SortingDefinitionProviderInterface> $providers
     */
    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    public function getDefaultSortingDefinition(): ?SortingDefinitionInterface
    {
        if ($this->definitions === null) {
            $this->initialize();
        }

        return $this->definitions[0] ?? null;
    }

    /**
     * @return SortingDefinitionInterface[]
     */
    public function getSortingDefinitions(): array
    {
        if ($this->definitions === null) {
            $this->initialize();
        }

        /** @var SortingDefinitionInterface[] */
        return $this->definitions;
    }

    private function initialize(): void
    {
        $this->definitions = [];
        foreach ($this->providers as $provider) {
            foreach ($provider->getSortingDefinitions() as $definition) {
                $this->definitions[] = $definition;
            }
        }

        usort(
            $this->definitions,
            static function (
                SortingDefinitionInterface $a,
                SortingDefinitionInterface $b
            ): int {
                return $a->getPriority() <=> $b->getPriority();
            }
        );
    }
}
