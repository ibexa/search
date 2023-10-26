<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Contracts\Search\Model\Suggestion;

use Ibexa\Contracts\Core\Collection\MutableArrayList;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Search\Model\Suggestion\ParentLocation;
use Ibexa\Contracts\Search\Model\Suggestion\ParentLocationCollection;
use PHPUnit\Framework\TestCase;

final class ParentCollectionTest extends TestCase
{
    public function testCollection(): void
    {
        $collection = new ParentLocationCollection();
        self::assertInstanceOf(MutableArrayList::class, $collection);
        self::assertInstanceOf(ParentLocationCollection::class, $collection);

        $collection->append(new ParentLocation(10, 1, 'text_1'));
        $collection->append(new ParentLocation(20, 2, 'text_2'));
        $collection->append(new ParentLocation(30, 3, 'text_3'));
        $collection->append(new ParentLocation(10, 4, 'text_4'));
        $collection->append(new ParentLocation(50, 5, 'text_5'));
        $collection->append(new ParentLocation(60, 6, 'text_6'));
        $collection->append(new ParentLocation(70, 7, 'text_7'));

        self::assertCount(7, $collection);

        foreach ($collection as $item) {
            self::assertInstanceOf(ParentLocation::class, $item);
        }

        self::expectException(InvalidArgumentException::class);
        $collection->append(new \stdClass());
    }
}
