<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\Service\Event;

use Ibexa\Contracts\Search\Event\AfterSuggestionEvent;
use Ibexa\Contracts\Search\Event\BeforeSuggestionEvent;
use Ibexa\Contracts\Search\Model\Suggestion\SuggestionCollection;
use Ibexa\Contracts\Search\Service\SuggestionServiceInterface;
use Ibexa\Search\Model\SuggestionQuery;
use Ibexa\Search\Service\Event\SuggestionService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SuggestionServiceTest extends TestCase
{
    /** @var \Ibexa\Contracts\Search\Service\SuggestionServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $innerServiceMock;

    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $eventDispatcherMock;

    protected function setUp(): void
    {
        $this->innerServiceMock = $this->createMock(SuggestionServiceInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
    }

    public function testSuggestWithoutPropagationStop(): void
    {
        $query = new SuggestionQuery('test', 10, 'eng-GB');
        $suggestionCollection = new SuggestionCollection();
        $callCount = 0;
        $this->eventDispatcherMock
            ->expects(self::exactly(2))
            ->method('dispatch')
            ->willReturnCallback(static function (Event $event) use (&$callCount, $query, $suggestionCollection): Event {
                ++$callCount;
                if ($callCount === 1) {
                    self::assertInstanceOf(BeforeSuggestionEvent::class, $event);

                    return new BeforeSuggestionEvent($query, $suggestionCollection);
                }

                self::assertInstanceOf(AfterSuggestionEvent::class, $event);

                return new AfterSuggestionEvent($query, $suggestionCollection);
            });

        $this->innerServiceMock
            ->expects($this->once())
            ->method('suggest')
            ->with($query)
            ->willReturn($suggestionCollection);

        $service = new SuggestionService($this->innerServiceMock, $this->eventDispatcherMock);
        $result = $service->suggest($query);

        self::assertEquals($suggestionCollection, $result);
    }

    public function testSuggestWithPropagationStop(): void
    {
        $query = new SuggestionQuery('test', 10, 'eng-GB');
        $suggestionCollection = new SuggestionCollection();
        $beforeEvent = new BeforeSuggestionEvent($query, $suggestionCollection);
        $beforeEvent->stopPropagation();

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn($beforeEvent);

        $this->innerServiceMock
            ->expects($this->never())
            ->method('suggest');

        $service = new SuggestionService($this->innerServiceMock, $this->eventDispatcherMock);
        $result = $service->suggest($query);

        self::assertEquals($suggestionCollection, $result);
    }
}
