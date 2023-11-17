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

    private ?string $languageCode;

    public function __construct(string $query, int $limit, ?string $languageCode = null)
    {
        $this->query = $query;
        $this->limit = $limit;
        $this->languageCode = $languageCode;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }
}
