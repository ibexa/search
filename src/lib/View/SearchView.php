<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\View;

use eZ\Publish\Core\MVC\Symfony\View\BaseView;

class SearchView extends BaseView
{
}

class_alias(SearchView::class, 'Ibexa\Platform\Search\View\SearchView');
