<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\Model\Suggestion;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Search\Model\Suggestion\ParentLocationCollection;

abstract class Suggestion extends ValueObject
{
    private float $score;

    private string $name;

    private string $pathString;

    private ParentLocationCollection $parentsLocation;

    /**
     * @param array<\Ibexa\Search\Model\Suggestion\ParentLocation> $parentLocations
     */
    public function __construct(
        float $score,
        string $name,
        string $pathString = '',
        array $parentLocations = []
    ) {
        $this->score = $score;
        $this->name = $name;
        $this->pathString = $pathString;
        $this->parentsLocation = new ParentLocationCollection($parentLocations);

        parent::__construct();
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPathString(): string
    {
        return $this->pathString;
    }

    public function getParentLocations(): ParentLocationCollection
    {
        return $this->parentsLocation;
    }

    abstract public function getType(): string;
}
