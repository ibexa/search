<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\Model\Suggestion;

use Ibexa\Contracts\Search\Model\Suggestion\ContentSuggestion;
use Ibexa\Contracts\Search\Model\Suggestion\ParentLocation;
use Ibexa\Contracts\Search\Model\Suggestion\Suggestion;
use Ibexa\Search\Model\Suggestion\ParentLocationCollection;
use Ibexa\Tests\Core\Search\TestCase;

final class ContentSuggestionTest extends TestCase
{
    public function testCreate(): void
    {
        $implementation = new ContentSuggestion(
            1,
            'content_type_identifier',
            'name',
            1,
            2,
            'text',
            [0 => new ParentLocation(0, 1, 'text')]
        );

        self::assertInstanceOf(Suggestion::class, $implementation);
        self::assertSame(1, $implementation->getContentId());
        self::assertSame('content_type_identifier', $implementation->getContentTypeIdentifier());
        self::assertInstanceOf(ParentLocationCollection::class, $implementation->getParentLocations());
        self::assertCount(1, $implementation->getParentLocations());
    }
}
