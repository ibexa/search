<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\Provider;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Search\Provider\ParentLocationProvider;
use PHPUnit\Framework\TestCase;

final class ParentLocationProviderTest extends TestCase
{
    public function testProvider(): void
    {
        $provider = new ParentLocationProvider($this->getLocationServiceMock());

        $parentLocations = $provider->provide([1, 2, 3]);

        self::assertCount(3, $parentLocations);
    }

    private function getLocationServiceMock(): LocationService
    {
        $locationServiceMock = $this->createMock(LocationService::class);
        $locationServiceMock->method('loadLocationList')->willReturnCallback(function (array $locationIds): iterable {
            foreach ($locationIds as $locationId) {
                yield $this->getLocation($locationId);
            }
        });

        return $locationServiceMock;
    }

    private function getLocation(int $locationId): Location
    {
        return new Location([
            'id' => $locationId,
            'contentInfo' => new ContentInfo([
                'id' => $locationId,
                'name' => 'name_' . $locationId,
            ]),
        ]);
    }
}
