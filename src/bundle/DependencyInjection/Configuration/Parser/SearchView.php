<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser\View;

class SearchView extends View
{
    public const NODE_KEY = 'search_view';
    public const INFO = 'Template for displaying main search form and results';
}

class_alias(SearchView::class, 'Ibexa\Platform\Bundle\Search\DependencyInjection\Configuration\Parser\SearchView');
