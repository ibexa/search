<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Service;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Search\EventDispatcher\Event\PostAutoCompleteSearch;
use Ibexa\Search\EventDispatcher\Event\PreAutoCompleteSearch;
use Ibexa\Search\Mapper\SearchResultToSuggestionCollectionMapper;
use Ibexa\Search\Model\SuggestionCollection;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SearchAutoCompleteService
{
    private SearchService $searchService;

    private EventDispatcherInterface $eventDispatcher;

    private SearchResultToSuggestionCollectionMapper $suggestionCollectionMapper;

    public function __construct(
        SearchService $searchService,
        EventDispatcherInterface $eventDispatcher,
        SearchResultToSuggestionCollectionMapper $suggestionCollectionMapper
    ) {
        $this->searchService = $searchService;
        $this->eventDispatcher = $eventDispatcher;
        $this->suggestionCollectionMapper = $suggestionCollectionMapper;
    }

    /**
     * @return \Ibexa\Search\Model\SuggestionCollection
     */
    public function suggest(string $value, int $limit, ?string $language = null): SuggestionCollection
    {
        $criterion = new Query\Criterion\FullText($value);
        $query = new Query(['filter' => $criterion, 'limit' => $limit]);
        /** @var \Ibexa\Search\EventDispatcher\Event\PreAutoCompleteSearch $preEvent */
        $preEvent = $this->eventDispatcher->dispatch(new PreAutoCompleteSearch($query));
        $searchResult = $this->searchService->findContent($preEvent->getQuery());

        $mappedResult = $this->suggestionCollectionMapper->transform($searchResult, $language);

        /** @var \Ibexa\Search\EventDispatcher\Event\PostAutoCompleteSearch $postEvent */
        $postEvent = $this->eventDispatcher->dispatch(new PostAutoCompleteSearch($mappedResult));

        return $postEvent->getSuggestionCollection();
    }
}
