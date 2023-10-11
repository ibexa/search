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

    /** @var array<int, string> */
    private array $parentsLocation;

    /**
     * @param array<int,string> $parentsLocation
     * @param array<mixed> $properties
     */
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

    /**
     * @return array<int, string>
     */
    public function getParentsLocation(): array
    {
        return $this->parentsLocation;
    }

    public function addPath(int $locationId, string $name): void
    {
        $this->parentsLocation[$locationId] = $name;
    }
}
