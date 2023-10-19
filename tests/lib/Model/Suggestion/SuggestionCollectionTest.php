<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\Model\Suggestion;

use Ibexa\Contracts\Core\Collection\MutableArrayList;
use Ibexa\Search\Model\Suggestion\ContentSuggestion;
use Ibexa\Search\Model\Suggestion\SuggestionCollection;
use PHPUnit\Framework\TestCase;

final class SuggestionCollectionTest extends TestCase
{
    public function testCollection(): void
    {
        $collection = new SuggestionCollection();
        $this->assertInstanceOf(MutableArrayList::class, $collection);
        $this->assertInstanceOf(SuggestionCollection::class, $collection);

        $collection->append(new ContentSuggestion(1, 'article', 'test'));
        $collection->append(new ContentSuggestion(2, 'article', 'test2'));

        $this->assertCount(2, $collection);

        foreach ($collection as $item) {
            $this->assertInstanceOf(ContentSuggestion::class, $item);
        }

        $this->expectException(\TypeError::class);
        $collection->append(new \stdClass());
    }
}
