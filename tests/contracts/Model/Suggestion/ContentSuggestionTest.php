<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Contracts\Search\Model\Suggestion;

use Ibexa\Contracts\Search\Model\Suggestion\ContentSuggestion;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Tests\Core\Search\TestCase;

final class ContentSuggestionTest extends TestCase
{
    public function testCreate(): void
    {
        $contentType = new ContentType();
        $content = new Content([
            'versionInfo' => new VersionInfo([
                'names' => ['eng-GB' => 'Test'],
                'initialLanguageCode' => 'eng-GB',
                'contentInfo' => [
                    'id' => 1,
                ],
            ]),
        ]);

        $implementation = new ContentSuggestion(
            1,
            $content,
            $contentType,
            '2/4/5',
            [new Location([
                'id' => 1,
                'path' => [2, 4, 5],
            ])]
        );

        self::assertSame($content, $implementation->getContent());
        self::assertCount(1, $implementation->getParentsLocation());
        self::assertSame('2/4/5', $implementation->getPathString());
        self::assertSame($contentType, $implementation->getContentType());
    }
}
