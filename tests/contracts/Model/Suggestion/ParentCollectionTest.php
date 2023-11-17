<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Contracts\Search\Model\Suggestion;

use Ibexa\Contracts\Core\Collection\MutableArrayList;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Persistence\Content\Location;
use Ibexa\Contracts\Search\Model\Suggestion\ParentLocationCollection;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ParentCollectionTest extends TestCase
{
    public function testCollection(): void
    {
        $collection = new ParentLocationCollection();
        self::assertInstanceOf(MutableArrayList::class, $collection);
        self::assertInstanceOf(ParentLocationCollection::class, $collection);

        $collection->append($this->getLocation(10));
        $collection->append($this->getLocation(10));
        $collection->append($this->getLocation(10));
        $collection->append($this->getLocation(40));
        $collection->append($this->getLocation(50));
        $collection->append($this->getLocation(60));
        $collection->append($this->getLocation(70));

        self::assertCount(7, $collection);

        foreach ($collection as $item) {
            self::assertInstanceOf(Location::class, $item);
        }

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            <<<'EOD'
Argument 1 passed to Ibexa\Contracts\Search\Model\Suggestion\ParentLocationCollection::append() 
must be an instance of Ibexa\Contracts\Core\Repository\Values\Content\Location, stdClass given
EOD
        );
        $collection->append(new stdClass());
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
