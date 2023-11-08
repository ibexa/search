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
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class SuggestionCollectionTest extends TestCase
{
    public function testCollection(): void
    {
        $collection = new SuggestionCollection();
        self::assertInstanceOf(MutableArrayList::class, $collection);
        self::assertInstanceOf(SuggestionCollection::class, $collection);

        $contentMock = $this->createMock(Content::class);
        $contentTypeMock = $this->createMock(ContentType::class);

        $collection->append(new ContentSuggestion(10, $contentMock, $contentTypeMock, '1/2/3', [new Location()]));
        $collection->append(new ContentSuggestion(20, $contentMock, $contentTypeMock, '1/3/5', [new Location()]));
        $collection->append(new ContentSuggestion(30, $contentMock, $contentTypeMock, '1/2/6', [new Location()]));
        $collection->append(new ContentSuggestion(10, $contentMock, $contentTypeMock, '1/3/4', [new Location()]));
        $collection->append(new ContentSuggestion(50, $contentMock, $contentTypeMock, '5/7/10', [new Location()]));
        $collection->append(new ContentSuggestion(60, $contentMock, $contentTypeMock, '5/2/1', [new Location()]));
        $collection->append(new ContentSuggestion(70, $contentMock, $contentTypeMock, '8/2/10', [new Location()]));

        self::assertCount(7, $collection);
        self::assertContainsOnlyInstancesOf(ContentSuggestion::class, $collection);

        $collection->sortByScore();
        self::assertSame(70.0, $collection->first()->getScore());

        $collection->truncate(5);
        self::assertCount(5, $collection);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            <<<'EOD'
Argument 1 passed to Ibexa\Contracts\Search\Model\Suggestion\SuggestionCollection::append() 
must be an instance of Ibexa\Contracts\Search\Model\Suggestion\Suggestion, stdClass given
EOD
        );
        $collection->append(new stdClass());
    }
}
