<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\Event\Service;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Search\Model\Suggestion\SuggestionCollection;
use Ibexa\Search\Model\SuggestionQuery;

final class BeforeSuggestEvent extends BeforeEvent
{
    private SuggestionQuery $query;

    private ?SuggestionCollection $suggestionCollection = null;

    public function __construct(SuggestionQuery $query)
    {
        $this->query = $query;
    }

    public function setQuery(SuggestionQuery $query): void
    {
        $this->query = $query;
    }

    public function getQuery(): SuggestionQuery
    {
        return $this->query;
    }

    public function getSuggestionCollection(): ?SuggestionCollection
    {
        return $this->suggestionCollection;
    }

    public function setSuggestionCollection(SuggestionCollection $suggestionCollection): void
    {
        $this->suggestionCollection = $suggestionCollection;
    }
}
