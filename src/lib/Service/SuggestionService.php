<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Service;

use Ibexa\Contracts\Search\Event\BuildSuggestionCollectionEvent;
use Ibexa\Contracts\Search\Model\Suggestion\SuggestionCollection;
use Ibexa\Contracts\Search\Service\SuggestionServiceInterface;
use Ibexa\Search\Model\SuggestionQuery;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SuggestionService implements SuggestionServiceInterface
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function suggest(SuggestionQuery $query): SuggestionCollection
    {
        /** @var \Ibexa\Contracts\Search\Event\BuildSuggestionCollectionEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new BuildSuggestionCollectionEvent(
                $query
            )
        );

        $collection = $event->getSuggestionCollection();
        $collection->sortByScore();
        $collection->truncate($query->getLimit());

        return $collection;
    }
}
