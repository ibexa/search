<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\SortingDefinition\Provider;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentName;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentTranslatedName;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinition;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionInterface;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionProviderInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class NameSortingDefinitionProvider implements SortingDefinitionProviderInterface, TranslationContainerInterface
{
    private RepositoryConfigurationProvider $configurationProvider;

    private TranslatorInterface $translator;

    public function __construct(RepositoryConfigurationProvider $configurationProvider, TranslatorInterface $translator)
    {
        $this->configurationProvider = $configurationProvider;
        $this->translator = $translator;
    }

    public function getSortingDefinitions(): array
    {
        return [
            $this->createSortingDefinition(200, false),
            $this->createSortingDefinition(300, true),
        ];
    }

    private function createSortingDefinition(int $priority, bool $reverse): SortingDefinitionInterface
    {
        $identifier = $this->getIdentifier($reverse);

        return new SortingDefinition(
            $identifier,
            $this->getLabel($identifier),
            $this->getSortClauses($reverse),
            $priority
        );
    }

    private function getIdentifier(bool $reverse): string
    {
        return 'name_' . ($reverse ? 'desc' : 'asc');
    }

    private function getLabel(string $identifier): string
    {
        return $this->translator->trans(
            /** @Ignore */
            sprintf('sort_definition.%s.label', $identifier),
            [],
            'ibexa_search'
        );
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[]
     */
    private function getSortClauses(bool $reverse): array
    {
        $direction = $reverse ? Query::SORT_DESC : Query::SORT_ASC;

        if ($this->isLegacySearchEngine()) {
            return [
                new ContentName($direction),
            ];
        }

        return [
            new ContentTranslatedName($direction),
        ];
    }

    private function isLegacySearchEngine(): bool
    {
        $config = $this->configurationProvider->getRepositoryConfig();

        return $config['search']['engine'] === 'legacy';
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('sort_definition.name_asc.label', 'ibexa_search'))->setDesc('Sort by name A-Z'),
            (new Message('sort_definition.name_desc.label', 'ibexa_search'))->setDesc('Sort by name Z-A'),
        ];
    }
}
