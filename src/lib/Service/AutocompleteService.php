<?php
/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Service;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Search\EventDispatcher\Event\PostAutocompleteSearch;
use Ibexa\Search\EventDispatcher\Event\PreAutocompleteSearch;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class AutocompleteService
{
    private SearchService $searchService;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        SearchService $searchService,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->searchService = $searchService;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function suggest(string $value, int $limit): array
    {
        $criterion = new Query\Criterion\FullText($value); //@todo properties
        $query = new Query(['filter' => $criterion, 'limit' => $limit]);
        $preEvent = $this->eventDispatcher->dispatch(new PreAutocompleteSearch($query));
        $result = $this->searchService->findContent($preEvent->getQuery());
        $postEvent = $this->eventDispatcher->dispatch(new PostAutocompleteSearch($result));

        return $postEvent->getSearchResult();
    }
}
