<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\Search;

use Ibexa\Bundle\Search\DependencyInjection\Configuration\Parser\Search;
use Ibexa\Bundle\Search\DependencyInjection\Configuration\Parser\SearchView;
use Ibexa\Bundle\Search\DependencyInjection\Configuration\Parser\SiteAccessAware\SuggestionParser;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaSearchBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $core */
        $core = $container->getExtension('ibexa');

        $core->addDefaultSettings(__DIR__ . '/Resources/config', ['default_settings.yaml']);
        $core->addConfigParser(new Search());
        $core->addConfigParser(new SearchView());
        $core->addConfigParser(new SuggestionParser());
    }
}

class_alias(IbexaSearchBundle::class, 'Ibexa\Platform\Bundle\Search\IbexaPlatformSearchBundle');
