<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Service\Event;

use Ibexa\Contracts\Search\Event\Service\BeforeSuggestEvent;
use Ibexa\Contracts\Search\Event\Service\SuggestEvent;
use Ibexa\Contracts\Search\Model\Suggestion\SuggestionCollection;
use Ibexa\Contracts\Search\Service\Decorator\SuggestionServiceDecorator;
use Ibexa\Contracts\Search\Service\SuggestionServiceInterface;
use Ibexa\Search\Model\SuggestionQuery;
use LogicException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SuggestionService extends SuggestionServiceDecorator
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        SuggestionServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function suggest(SuggestionQuery $query): SuggestionCollection
    {
        $beforeEvent = $this->eventDispatcher->dispatch(
            new BeforeSuggestEvent($query)
        );

        if ($beforeEvent->isPropagationStopped()) {
            $suggestionCollection = $beforeEvent->getSuggestionCollection();
            if ($suggestionCollection === null) {
                throw new LogicException(
                    'The suggestion collection must be set when the propagation is stopped.'
                );
            }

            return $suggestionCollection;
        }

        $result = $this->innerService->suggest($beforeEvent->getQuery());
        $afterEvent = $this->eventDispatcher->dispatch(
            new SuggestEvent(
                $query,
                $result
            )
        );

        return $afterEvent->getSuggestionCollection();
    }
}
