<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Contracts\Search\Model\Suggestion;

use Ibexa\Contracts\Search\Model\Suggestion\ParentLocation;
use Ibexa\Contracts\Search\Model\Suggestion\Suggestion;
use PHPUnit\Framework\TestCase;

final class SuggestionTest extends TestCase
{
    public function testSuggestionCreate(): void
    {
        $implementation = self::createSuggestion(
            0,
            'name',
            '2/4/5',
            [
                new ParentLocation(10, 1, 'text_1'),
            ]
        );

        self::assertInstanceOf(Suggestion::class, $implementation);
        self::assertSame('name', $implementation->getName());
        self::assertSame('2/4/5', $implementation->getPathString());
        self::assertCount(1, $implementation->getParentLocations());
    }

    /**
     * @param array<\Ibexa\Contracts\Search\Model\Suggestion\ParentLocation> $parentLocations
     */
    private function createSuggestion(
        int $score,
        string $name,
        string $pathString = '',
        array $parentLocations = []
    ): Suggestion {
        return new class($score, $name, $pathString, $parentLocations) extends Suggestion {
            public function getType(): string
            {
                return 'test_implementation';
            }
        };
    }
}
