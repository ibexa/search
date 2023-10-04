<?php
/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\EventDispatcher\Event;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;

class PostAutocompleteSearch
{
    private SearchResult $searchResult;

    public function __construct(SearchResult $searchResult)
    {
        $this->searchResult = $searchResult;
    }

    public function getSearchResult(): SearchResult
    {
        return $this->searchResult;
    }
}
