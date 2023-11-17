<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\SortingDefinition\Provider;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentName;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentTranslatedName;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinition;
use Ibexa\Search\SortingDefinition\Provider\NameSortingDefinitionProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class NameSortingDefinitionProviderTest extends TestCase
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface&\PHPUnit\Framework\MockObject\MockObject */
    private TranslatorInterface $translator;

    /** @var \Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider&\PHPUnit\Framework\MockObject\MockObject */
    private RepositoryConfigurationProvider $configurationProvider;

    private NameSortingDefinitionProvider $provider;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->translator->method('trans')->willReturnArgument(0);

        $this->configurationProvider = $this->createMock(RepositoryConfigurationProvider::class);

        $this->provider = new NameSortingDefinitionProvider(
            $this->configurationProvider,
            $this->translator
        );
    }

    public function testGetSortingDefinitionsForLSE(): void
    {
        $this->setSearchEngine('legacy');

        $this->assertEquals(
            [
                new SortingDefinition(
                    'name_asc',
                    'sort_definition.name_asc.label',
                    [
                        new ContentName(Query::SORT_ASC),
                    ],
                    200,
                ),
                new SortingDefinition(
                    'name_desc',
                    'sort_definition.name_desc.label',
                    [
                        new ContentName(Query::SORT_DESC),
                    ],
                    300,
                ),
            ],
            $this->provider->getSortingDefinitions()
        );
    }

    public function testGetSortingDefinitionsForNonLSE(): void
    {
        $this->setSearchEngine('solr');

        $this->assertEquals(
            [
                new SortingDefinition(
                    'name_asc',
                    'sort_definition.name_asc.label',
                    [
                        new ContentTranslatedName(Query::SORT_ASC),
                    ],
                    200,
                ),
                new SortingDefinition(
                    'name_desc',
                    'sort_definition.name_desc.label',
                    [
                        new ContentTranslatedName(Query::SORT_DESC),
                    ],
                    300,
                ),
            ],
            $this->provider->getSortingDefinitions()
        );
    }

    private function setSearchEngine(string $engine): void
    {
        $this->configurationProvider
            ->method('getRepositoryConfig')
            ->willReturn([
                'search' => [
                    'engine' => $engine,
                ],
            ]);
    }
}
