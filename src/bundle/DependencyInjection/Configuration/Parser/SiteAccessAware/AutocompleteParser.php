<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\DependencyInjection\Configuration\Parser\SiteAccessAware;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Configuration parser for search.
 *
 * @Example configuration:
 *
 * ```yaml
 * ibexa:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          search:
 *              autocomplete:
 *                  min_search_test_length: 3
 *                  result_limit: 5
 * ```
 */
final class AutocompleteParser extends AbstractParser
{
    /**
     * @param array<string, mixed> $scopeSettings
     */
    public function mapConfig(
        array &$scopeSettings,
        $currentScope,
        ContextualizerInterface $contextualizer
    ): void {
        if (empty($scopeSettings['search'])) {
            return;
        }

        $settings = $scopeSettings['search'];

        $this->addAutocompleteParameters($settings, $currentScope, $contextualizer);
    }

    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $rootProductCatalogNode = $nodeBuilder->arrayNode('search');
        $rootProductCatalogNode->append($this->addAutocompleteConfiguration());
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function addAutocompleteParameters(
        $settings,
        string $currentScope,
        ContextualizerInterface $contextualizer
    ): void {
        $names = [
            'min_search_test_length',
            'result_limit',
        ];
        foreach ($names as $name) {
            if (isset($settings['autocomplete'][$name])) {
                $contextualizer->setContextualParameter(
                    'search.autocomplete.' . $name,
                    $currentScope,
                    $settings['autocomplete'][$name]
                );
            }
        }
    }

    private function addAutocompleteConfiguration(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('autocomplete');
        $node = $treeBuilder->getRootNode();

        $node
            ->children()
                ->integerNode('min_search_test_length')
                    ->isRequired()
                    ->defaultValue(3)
                    ->min(3)
                ->end()
                ->integerNode('result_limit')
                    ->isRequired()
                    ->defaultValue(5)
                    ->min(5)
                ->end()
            ->end();

        return $node;
    }
}
