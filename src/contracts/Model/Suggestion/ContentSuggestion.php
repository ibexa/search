<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\Model\Suggestion;

final class ContentSuggestion extends Suggestion
{
    private int $contentId;

    private int $locationId;

    private string $contentTypeIdentifier;

    /**
     * @param array<\Ibexa\Contracts\Search\Model\Suggestion\ParentLocation> $parentLocations
     */
    public function __construct(
        float $score,
        string $contentTypeIdentifier,
        string $name,
        int $contentId,
        int $locationId,
        string $pathString = '',
        array $parentLocations = []
    ) {
        parent::__construct($score, $name, $pathString, $parentLocations);
        $this->contentId = $contentId;
        $this->contentTypeIdentifier = $contentTypeIdentifier;
        $this->locationId = $locationId;
    }

    public function getContentId(): int
    {
        return $this->contentId;
    }

    public function getLocationId(): int
    {
        return $this->locationId;
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
