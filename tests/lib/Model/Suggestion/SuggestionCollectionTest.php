<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\Model\Suggestion;

use Ibexa\Contracts\Core\Collection\MutableArrayList;
use Ibexa\Contracts\Search\Model\Suggestion\ContentSuggestion;
use Ibexa\Search\Model\Suggestion\SuggestionCollection;
use PHPUnit\Framework\TestCase;

final class SuggestionCollectionTest extends TestCase
{
    public function testCollection(): void
    {
        $collection = new SuggestionCollection();
        self::assertInstanceOf(MutableArrayList::class, $collection);
        self::assertInstanceOf(SuggestionCollection::class, $collection);

        $collection->append(new ContentSuggestion(10, 'article', 'test', 1, 2));
        $collection->append(new ContentSuggestion(20, 'article', 'test2', 1, 2));
        $collection->append(new ContentSuggestion(30, 'article', 'test3', 1, 2));
        $collection->append(new ContentSuggestion(10, 'article', 'test4', 1, 2));
        $collection->append(new ContentSuggestion(50, 'article', 'test5', 1, 2));
        $collection->append(new ContentSuggestion(60, 'article', 'test6', 1, 2));
        $collection->append(new ContentSuggestion(70, 'article', 'test7', 1, 2));

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
