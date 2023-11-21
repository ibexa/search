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
 * Example configuration:
 *
 * ```yaml
 * ibexa:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          search:
 *              suggestion:
 *                  min_query_length: 3
 *                  result_limit: 5
 * ```
 */
final class SuggestionParser extends AbstractParser
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

        $this->addSuggestionParameters($settings, $currentScope, $contextualizer);
    }

    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $rootProductCatalogNode = $nodeBuilder->arrayNode('search');
        $rootProductCatalogNode->append($this->addSuggestionConfiguration());
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function addSuggestionParameters(
        array $settings,
        string $currentScope,
        ContextualizerInterface $contextualizer
    ): void {
        $names = [
            'min_query_length',
            'result_limit',
        ];

        foreach ($names as $name) {
            if (isset($settings['suggestion'][$name])) {
                $contextualizer->setContextualParameter(
                    'search.suggestion.' . $name,
                    $currentScope,
                    $settings['suggestion'][$name]
                );
            }
        }
    }

    private function addSuggestionConfiguration(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('suggestion');
        $node = $treeBuilder->getRootNode();

        $node
            ->children()
                ->integerNode('min_query_length')
                    ->info('The minimum length of the query string needed to trigger suggestions. Minimum value is 3.')
                    ->isRequired()
                    ->defaultValue(3)
                    ->min(3)
                ->end()
                ->integerNode('result_limit')
                    ->info('The maximum number of suggestion results to return. Minimum value is 5.')
                    ->isRequired()
                    ->defaultValue(5)
                    ->min(5)
                ->end()
            ->end();

        return $node;
    }
}
