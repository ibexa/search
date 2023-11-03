<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\Model\Suggestion;

final class ParentLocation
{
    private int $id;

    private int $locationId;

    private string $name;

    public function __construct(int $id, int $locationId, string $name)
    {
        $this->id = $id;
        $this->locationId = $locationId;
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
