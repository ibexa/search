<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\EventDispatcher\EventListener;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Search\EventDispatcher\Event\AbstractSuggestion;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class PathSubscriber implements EventSubscriberInterface
{
    private LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AbstractSuggestion::class => 'onSuggestion',
        ];
    }

    public function onSuggestion(AbstractSuggestion $event): AbstractSuggestion
    {
        foreach ($event->getSuggestionCollection() as $suggestion) {
            foreach ($suggestion->get() as $locationId => $name) {
                try {
                    $location = $this->locationService->loadLocation($locationId);
                    $suggestion->addPath($locationId, $location->contentInfo->name);
                } catch (\Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException $e) {
                } catch (\Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException $e) {
                }
            }
        }

        return $event;
    }
}
