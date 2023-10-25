<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Provider;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Search\Model\Suggestion\ParentLocation;
use Ibexa\Contracts\Search\Provider\ParentLocationProviderInterface;

final class ParentLocationProvider implements ParentLocationProviderInterface
{
    private LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * @param array<int> $parentLocationIds
     *
     * @return array<\Ibexa\Contracts\Search\Model\Suggestion\ParentLocation>
     */
    public function provide(array $parentLocationIds): array
    {
        $parentLocations = $this->locationService->loadLocationList($parentLocationIds);
        $parentLocationMap = [];
        foreach ($parentLocations as $parentLocation) {
            $parentLocationMap[] = new ParentLocation(
                $parentLocation->getContentInfo()->id,
                $parentLocation->id,
                $parentLocation->getContentInfo()->name
            );
        }

        return $parentLocationMap;
    }
}
