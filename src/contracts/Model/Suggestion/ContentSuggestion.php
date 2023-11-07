<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\Model\Suggestion;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

final class ContentSuggestion extends Suggestion
{
    private Content $content;

    private ContentType $contentType;

    private string $pathString;

    private ParentLocationCollection $parentsLocation;

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Location> $parentLocations
     */
    public function __construct(
        float $score,
        Content $content,
        ContentType $contentType,
        string $pathString = '',
        array $parentLocations = []
    ) {
        parent::__construct($score, $content->getName());
        $this->content = $content;
        $this->contentType = $contentType;
        $this->pathString = $pathString;
        $this->parentsLocation = new ParentLocationCollection($parentLocations);
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    public function getPathString(): string
    {
        return $this->pathString;
    }

    public function getParentsLocation(): ParentLocationCollection
    {
        return $this->parentsLocation;
    }
}
