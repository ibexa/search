<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\Model\Suggestion;

use Ibexa\Search\Model\Suggestion\Suggestion as SuggestionAlias;
use PHPUnit\Framework\TestCase;

final class SuggestionTest extends TestCase
{
    public function testSuggestionCreate(): void
    {
        $implementation = new class(50, 'name', 'text', [0 => null]) extends SuggestionAlias {
            public function getType(): string
            {
                return 'test_implementation';
            }
        };

        $this->assertInstanceOf(SuggestionAlias::class, $implementation);
        $this->assertSame('name', $implementation->getName());
        $this->assertSame('text', $implementation->getPathString());
        $this->assertSame([0 => null], $implementation->getParentsLocation());
        $this->assertSame('test_implementation', $implementation->getType());

        $implementation->addPath(0, 'text');
        $implementation->addPath(1, 'text2');
        $this->assertSame([0 => 'text', 1 => 'text2'], $implementation->getParentsLocation());
    }
}
