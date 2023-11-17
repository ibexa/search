<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\SortingDefinition;

use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionInterface;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionProviderInterface;
use Ibexa\Search\SortingDefinition\SortingDefinitionRegistry;
use PHPUnit\Framework\TestCase;

final class SortingDefinitionRegistryTest extends TestCase
{
    public function testGetDefaultSortingDefinition(): void
    {
        $foo = $this->createSortingDefinition(100);
        $bar = $this->createSortingDefinition(200);
        $baz = $this->createSortingDefinition(300);

        $registry = new SortingDefinitionRegistry([
            $this->createProvider([$bar, $baz]),
            $this->createProvider([$foo]),
        ]);

        self::assertSame($foo, $registry->getDefaultSortingDefinition());
    }

    public function testGetDefaultSortingDefinitionIfRegistryIsEmpty(): void
    {
        $registry = new SortingDefinitionRegistry([
            /* No providers */
        ]);

        self::assertNull($registry->getDefaultSortingDefinition());
    }

    public function testGetSortingDefinitions(): void
    {
        $foo = $this->createSortingDefinition(100);
        $bar = $this->createSortingDefinition(200);
        $baz = $this->createSortingDefinition(300);

        $registry = new SortingDefinitionRegistry([
            $this->createProvider([$bar, $baz]),
            $this->createProvider([$foo]),
        ]);

        self::assertEquals([$foo, $bar, $baz], $registry->getSortingDefinitions());
    }

    /**
     * @param \Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionInterface[] $definitions
     */
    private function createProvider(array $definitions): SortingDefinitionProviderInterface
    {
        $provider = $this->createMock(SortingDefinitionProviderInterface::class);
        $provider->method('getSortingDefinitions')->willReturn($definitions);

        return $provider;
    }

    private function createSortingDefinition(int $priority): SortingDefinitionInterface
    {
        $definition = $this->createMock(SortingDefinitionInterface::class);
        $definition->method('getPriority')->willReturn($priority);

        return $definition;
    }
}
