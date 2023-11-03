<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\Model\Suggestion;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

final class ContentSuggestion extends Suggestion
{
    private int $contentId;

    private ?int $locationId;

    private ContentType $contentType;

    private string $pathString;

    private ParentLocationCollection $parentsLocation;

    /**
     * @param array<\Ibexa\Contracts\Search\Model\Suggestion\ParentLocation> $parentLocations
     */
    public function __construct(
        float $score,
        ContentType $contentType,
        string $name,
        int $contentId,
        ?int $locationId = null,
        string $pathString = '',
        array $parentLocations = []
    ) {
        parent::__construct($score, $name);
        $this->contentId = $contentId;
        $this->contentType = $contentType;
        $this->locationId = $locationId;
        $this->pathString = $pathString;
        $this->parentsLocation = new ParentLocationCollection($parentLocations);
    }

    public function getContentId(): int
    {
        return $this->contentId;
    }

    public function getLocationId(): ?int
    {
        return $this->locationId;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    public function getPathString(): string
    {
        return $this->pathString;
    }

    public function getParentLocations(): ParentLocationCollection
    {
        return $this->parentsLocation;
    }
}
