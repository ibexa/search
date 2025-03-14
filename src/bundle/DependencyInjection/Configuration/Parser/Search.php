<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class Search extends AbstractParser
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('search')
                ->info('Search configuration')
                ->children()
                    ->arrayNode('pagination')
                        ->info('Pagination related configuration')
                        ->children()
                            ->integerNode('limit')
                                ->info('Number of results per page')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['search'])) {
            return;
        }

        $settings = $scopeSettings['search'];

        if (!empty($settings['pagination']['limit'])) {
            $contextualizer->setContextualParameter(
                'search.pagination.limit',
                $currentScope,
                $settings['pagination']['limit']
            );
        }
    }
}
