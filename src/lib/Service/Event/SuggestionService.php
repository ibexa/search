<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Service\Event;

use Ibexa\Contracts\Search\Event\AfterSuggestionEvent;
use Ibexa\Contracts\Search\Event\BeforeSuggestionEvent;
use Ibexa\Contracts\Search\Service\Decorator\SuggestionServiceDecorator;
use Ibexa\Search\Model\Suggestion\SuggestionCollection;
use Ibexa\Search\Model\SuggestionQuery;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SuggestionService extends SuggestionServiceDecorator
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        SuggestionServiceDecorator $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function suggest(SuggestionQuery $query): SuggestionCollection
    {
        $beforeEvent = $this->eventDispatcher->dispatch(
            new BeforeSuggestionEvent(
                $query,
                new SuggestionCollection()
            )
        );

        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getSuggestionCollection();
        }

        $result = $this->innerService->suggest($beforeEvent->getQuery());

        $afterEvent = $this->eventDispatcher->dispatch(
            new AfterSuggestionEvent(
                $query,
                $result
            )
        );

        return $afterEvent->getSuggestionCollection();
    }
}
