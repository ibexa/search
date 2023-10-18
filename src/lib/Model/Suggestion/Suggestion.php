<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Model\Suggestion;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

abstract class Suggestion extends ValueObject
{
    private string $name;

    private string $pathString;

    /** @var array<int, string> */
    private array $parentsLocation;

    /**
     * @param array<int,string> $parentsLocation
     */
    public function __construct(
        string $name,
        string $pathString = '',
        array $parentsLocation = []
    ) {
        $this->name = $name;
        $this->pathString = $pathString;
        $this->parentsLocation = $parentsLocation;

        parent::__construct();
    }

    public function getName(): string
    {
        return $this->name;
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

    abstract public function getType(): string;
}
