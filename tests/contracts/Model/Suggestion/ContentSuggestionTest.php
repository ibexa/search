<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Contracts\Search\Model\Suggestion;

use Ibexa\Contracts\Search\Model\Suggestion\ContentSuggestion;
use Ibexa\Contracts\Search\Model\Suggestion\ParentLocation;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Tests\Core\Search\TestCase;

final class ContentSuggestionTest extends TestCase
{
    public function testCreate(): void
    {
        $implementation = new ContentSuggestion(
            1,
            $this->createMock(ContentType::class),
            'name',
            1,
            2,
            'text',
            [0 => new ParentLocation(0, 1, 'text')]
        );

        self::assertSame(1, $implementation->getContentId());
        self::assertCount(1, $implementation->getParentLocations());
        self::assertSame('2/4/5', $implementation->getPathString());
        self::assertCount(1, $implementation->getParentLocations());
    }
}
