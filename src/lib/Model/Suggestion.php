<?php
/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Model;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class Suggestion extends ValueObject
{
    private int $contentId;
    private string $contentName;
    private string $contentTypeIdentifier;
    private string $pathString;
    private array $parentsLocation;

    public function __construct(
        int $contentId,
        string $contentName,
        string $contentTypeIdentifier,
        string $pathString,
        array $parentsLocation,
        array $properties = []
    ) {
        $this->contentId = $contentId;
        $this->contentName = $contentName;
        $this->contentTypeIdentifier = $contentTypeIdentifier;
        $this->pathString = $pathString;
        $this->parentsLocation = $parentsLocation;

        parent::__construct($properties);
    }

    public function getContentId(): int
    {
        return $this->contentId;
    }

    public function getContentName(): string
    {
        return $this->contentName;
    }

    public function getContentTypeIdentifier(): string
    {
        return $this->contentTypeIdentifier;
    }

    public function getPathString(): string
    {
        return $this->pathString;
    }

    public function getParentsLocation(): array
    {
        return $this->parentsLocation;
    }

    public function addPath(int $locationId, string $name)
    {
        $this->parentsLocation[$locationId] = $name;
    }
}
