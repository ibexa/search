<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Contracts\Search\Model\Suggestion;

use Ibexa\Contracts\Search\Model\Suggestion\Suggestion;
use PHPUnit\Framework\TestCase;

final class SuggestionTest extends TestCase
{
    public function testSuggestionCreate(): void
    {
        $implementation = $this->createSuggestion(
            0,
            'name'
        );

        self::assertInstanceOf(Suggestion::class, $implementation);
        self::assertSame('name', $implementation->getName());
    }

    private function createSuggestion(
        int $score,
        string $name
    ): Suggestion {
        return new class($score, $name) extends Suggestion {
            public function getType(): string
            {
                return 'test_implementation';
            }
        };
    }
}
