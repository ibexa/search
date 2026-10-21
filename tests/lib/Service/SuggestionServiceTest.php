<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\Service;

use Ibexa\Search\Model\SuggestionQuery;
use Ibexa\Search\Service\SuggestionService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SuggestionServiceTest extends TestCase
{
    public function testSuggestion(): void
    {
        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnArgument(0);

        $service = new SuggestionService($eventDispatcherMock);
        $result = $service->suggest(new SuggestionQuery('query', 10, 'eng-GB'));

        self::assertCount(0, $result);
    }
}
