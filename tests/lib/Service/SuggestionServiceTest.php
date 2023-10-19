<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\Service;

use Ibexa\Search\Model\Suggestion\SuggestionCollection;
use Ibexa\Search\Model\SuggestionQuery;
use Ibexa\Search\Service\SuggestionService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SuggestionServiceTest extends TestCase
{
    public function testSuggestion(): void
    {
        $eventDispatcherMock = $this->getEventDispatcherMock();
        $eventDispatcherMock
            ->method('dispatch')
            ->willReturnCallback(
                static function ($event) {
                    return $event;
                }
            );

        $service = new SuggestionService($eventDispatcherMock);
        $result = $service->suggest(new SuggestionQuery('query', 10, 'eng-GB'));

        self::assertInstanceOf(SuggestionCollection::class, $result);
    }

    private function getEventDispatcherMock(): EventDispatcherInterface
    {
        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $eventDispatcherMock;
    }
}
