<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\Model\Suggestion;

final class ParentLocation
{
    private int $contentId;

    private int $locationId;

    private string $name;

    public function __construct(int $contentId, int $locationId, string $name)
    {
        $this->contentId = $contentId;
        $this->locationId = $locationId;
        $this->name = $name;
    }

    public function getContentId(): int
    {
        return $this->contentId;
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
