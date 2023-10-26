<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\Event;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Search\Model\Suggestion\SuggestionCollection;
use Ibexa\Search\Model\SuggestionQuery;

final class BeforeSuggestionEvent extends BeforeEvent
{
    private SuggestionQuery $query;

    private SuggestionCollection $suggestionCollection;

    public function __construct(SuggestionQuery $query, SuggestionCollection $suggestionCollection)
    {
        $this->query = $query;
        $this->suggestionCollection = $suggestionCollection;
    }

    public function setQuery(SuggestionQuery $query): void
    {
        $this->query = $query;
    }

    public function getQuery(): SuggestionQuery
    {
        return $this->query;
    }

    public function getSuggestionCollection(): SuggestionCollection
    {
        return $this->suggestionCollection;
    }

    public function setSuggestionCollection(SuggestionCollection $suggestionCollection): void
    {
        $this->suggestionCollection = $suggestionCollection;
    }
}
