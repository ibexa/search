<?php
/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\EventDispatcher\EventListener;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Search\EventDispatcher\Event\PostAutoCompleteSearch;
use Ibexa\Search\Model\Suggestion;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class BreadCrumbSubscriber implements EventSubscriberInterface
{
    private LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostAutoCompleteSearch::class => 'onPostAutoCompleteSearch',
        ];
    }

    public function onPostAutoCompleteSearch(PostAutoCompleteSearch $event): PostAutoCompleteSearch
    {
        /** @var Suggestion $suggestion */
        foreach ($event->getSuggestionCollection() as $suggestion) {
            foreach ($suggestion->getParentsLocation() as $locationId => $name) {
                $location = $this->locationService->loadLocation($locationId);
                $suggestion->addPath($locationId, $location->contentInfo->name);
            }
        }

        return $event;
    }
}
