<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\EventDispatcher\Event;

use Ibexa\Search\Model\Suggestion\SuggestionCollection;
use Ibexa\Search\Model\SuggestionQuery;

abstract class AbstractSuggestion
{
    private SuggestionCollection $suggestionCollection;

    private SuggestionQuery $query;

    public function __construct(SuggestionQuery $query)
    {
        $this->suggestionCollection = new SuggestionCollection();
        $this->query = $query;
    }

    public function getSuggestionCollection(): SuggestionCollection
    {
        return $this->suggestionCollection;
    }
}
