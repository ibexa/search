<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\Service\Event;

use Ibexa\Contracts\Search\Event\AfterSuggestionEvent;
use Ibexa\Contracts\Search\Event\BeforeSuggestionEvent;
use Ibexa\Contracts\Search\Service\SuggestionServiceInterface;
use Ibexa\Search\Model\Suggestion\SuggestionCollection;
use Ibexa\Search\Model\SuggestionQuery;
use Ibexa\Search\Service\Event\SuggestionService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SuggestionServiceTest extends TestCase
{
    public function testSuggestion(): void
    {
        $eventDispatcherMock = $this->getEventDispatcherMock();
        $eventDispatcherMock
            ->method('dispatch')
            ->withConsecutive(
                [
                    $this->isInstanceOf(BeforeSuggestionEvent::class),
                ],
                [
                    $this->isInstanceOf(AfterSuggestionEvent::class),
                ]
            )
            ->willReturnCallback(
                static function ($event) {
                    return $event;
                }
            );

        $innerServiceMock = $this->getSuggestionServiceMock();
        $innerServiceMock
            ->method('suggest')
            ->willReturn(new SuggestionCollection());

        $service = new SuggestionService($innerServiceMock, $eventDispatcherMock);

        $result = $service->suggest(new SuggestionQuery('query', 10, 'eng-GB'));
        self::assertInstanceOf(SuggestionCollection::class, $result);
    }

    public function testSuggestionStopPropagation(): void
    {
        $eventDispatcherMock = $this->getEventDispatcherMock();
        $eventDispatcherMock
            ->expects(self::once())
            ->method('dispatch')
            ->withConsecutive(
                [
                    $this->isInstanceOf(BeforeSuggestionEvent::class),
                ]
            )
            ->willReturnCallback(
                static function (BeforeSuggestionEvent $event) {
                    $event->stopPropagation();

                    return $event;
                }
            );

        $innerServiceMock = $this->getSuggestionServiceMock();
        $innerServiceMock
            ->method('suggest')
            ->willReturn(new SuggestionCollection());

        $service = new SuggestionService($innerServiceMock, $eventDispatcherMock);

        $result = $service->suggest(new SuggestionQuery('query', 10, 'eng-GB'));
        self::assertInstanceOf(SuggestionCollection::class, $result);
    }

    private function getSuggestionServiceMock(): SuggestionServiceInterface
    {
        $suggestionServiceMock = $this->getMockBuilder(SuggestionServiceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $suggestionServiceMock;
    }

    private function getEventDispatcherMock(): EventDispatcherInterface
    {
        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $eventDispatcherMock;
    }
}
