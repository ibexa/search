<?php
/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Mapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Search\Model\Suggestion;
use Ibexa\Search\Model\SuggestionCollection;

final class SearchResultToSuggestionCollectionMapper
{
    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function transform(SearchResult $searchResult, ?string $language = null): SuggestionCollection
    {
        $rootLocationId = $this->configResolver->getParameter('content.tree_root.location_id');
        $collection = [];
        /** @var \Ibexa\Core\Repository\Values\Content\Content $result */
        foreach ($searchResult as $result) {
            /** @var \Ibexa\Core\Repository\Values\Content\Content $content */
            $content = $result->valueObject;

            $parentsLocation = $result->valueObject->contentInfo->mainLocation->path;
            $position = array_search((string)$rootLocationId, $parentsLocation);
            if ($position !== false) {
                $parentsLocation = array_slice($parentsLocation, $position);
            }

            $collection[] = new Suggestion(
                $result->valueObject->contentInfo->id,
                $content->getFieldValue('title', $language)->text,
                $result->valueObject->contentInfo->getContentType()->identifier,
                implode('/', $parentsLocation),
                array_flip($parentsLocation)
            );
        }

        return new SuggestionCollection($collection);
    }
}
