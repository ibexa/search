<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\Model\Suggestion;

use Ibexa\Search\Model\Suggestion\ContentSuggestion;
use Ibexa\Search\Model\Suggestion\Suggestion;
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
            'text',
            [0 => 'text']
        );

        $this->assertInstanceOf(Suggestion::class, $implementation);
        $this->assertSame(1, $implementation->getContentId());
        $this->assertSame('content_type_identifier', $implementation->getContentTypeIdentifier());
        $this->assertSame('content', $implementation->getType());
        $this->assertSame([0 => 'text'], $implementation->getParentsLocation());

        $implementation->addPath(1, 'text2');
        $this->assertSame([0 => 'text', 1 => 'text2'], $implementation->getParentsLocation());
    }
}
