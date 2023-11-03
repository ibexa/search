<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Contracts\Search\Model\Suggestion;

use Ibexa\Contracts\Core\Collection\MutableArrayList;
use Ibexa\Contracts\Search\Model\Suggestion\ContentSuggestion;
use Ibexa\Contracts\Search\Model\Suggestion\SuggestionCollection;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use PHPUnit\Framework\TestCase;

final class SuggestionCollectionTest extends TestCase
{
    public function testCollection(): void
    {
        $collection = new SuggestionCollection();
        self::assertInstanceOf(MutableArrayList::class, $collection);
        self::assertInstanceOf(SuggestionCollection::class, $collection);

        $contentTypeMock = $this->createMock(ContentType::class);

        $collection->append(new ContentSuggestion(10, $contentTypeMock, 'test', 1, 2));
        $collection->append(new ContentSuggestion(20, $contentTypeMock, 'test2', 1, 2));
        $collection->append(new ContentSuggestion(30, $contentTypeMock, 'test3', 1, 2));
        $collection->append(new ContentSuggestion(10, $contentTypeMock, 'test4', 1, 2));
        $collection->append(new ContentSuggestion(50, $contentTypeMock, 'test5', 1, 2));
        $collection->append(new ContentSuggestion(60, $contentTypeMock, 'test6', 1, 2));
        $collection->append(new ContentSuggestion(70, $contentTypeMock, 'test7', 1, 2));

        self::assertCount(7, $collection);

        foreach ($collection as $item) {
            self::assertInstanceOf(ContentSuggestion::class, $item);
        }

        $collection->sortByScore();
        self::assertSame(70.0, $collection->first()->getScore());

        $collection->truncate(5);
        self::assertCount(5, $collection);
    }
}
