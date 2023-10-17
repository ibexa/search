<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Model;

final class SuggestionQuery
{
    private string $query;

    private int $limit;

    private ?string $language;

    public function __construct(string $query, int $limit, ?string $language = null)
    {
        $this->query = $query;
        $this->limit = $limit;
        $this->language = $language;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }
}
