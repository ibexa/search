<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Model\Suggestion;

use Ibexa\Contracts\Search\Model\Suggestion\Suggestion;

final class ContentSuggestion extends Suggestion
{
    private int $contentId;

    private string $contentTypeIdentifier;

    /**
     * @param array<\Ibexa\Search\Model\Suggestion\ParentLocation> $parentLocations
     */
    public function __construct(
        float $score,
        string $contentTypeIdentifier,
        string $name,
        int $contentId,
        string $pathString = '',
        array $parentLocations = []
    ) {
        parent::__construct($score, $name, $pathString, $parentLocations);
        $this->contentId = $contentId;
        $this->contentTypeIdentifier = $contentTypeIdentifier;
    }

    public function getContentId(): int
    {
        return $this->contentId;
    }

    public function getContentTypeIdentifier(): string
    {
        return $this->contentTypeIdentifier;
    }

    public function getType(): string
    {
        return 'content';
    }
}
