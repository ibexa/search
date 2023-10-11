<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\EventDispatcher\Event;

use Ibexa\Search\Model\SuggestionCollection;

class PostAutoCompleteSearch
{
    private SuggestionCollection $suggestionCollection;

    public function __construct(SuggestionCollection $suggestionCollection)
    {
        $this->suggestionCollection = $suggestionCollection;
    }

    public function getSuggestionCollection(): SuggestionCollection
    {
        return $this->suggestionCollection;
    }
}
