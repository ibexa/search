<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Controller;

use Ibexa\Search\View\SearchView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    public function searchAction(SearchView $view): SearchView
    {
        return $view;
    }
}

class_alias(SearchController::class, 'Ibexa\Platform\Bundle\Search\Controller\SearchController');
