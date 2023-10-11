<?php
/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Mapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Search\Model\Suggestion;
use Ibexa\Search\Model\SuggestionCollection;

class SearchResultToSuggestionCollectionMapper
{
    public function transform(SearchResult $searchResult, ?string $language = null): SuggestionCollection
    {
        $collection = [];
        /** @var \Ibexa\Core\Repository\Values\Content\Content $result */
        foreach ($searchResult as $result) {
            /** @var \Ibexa\Core\Repository\Values\Content\Content $content */
            $content = $result->valueObject;

            $collection[] = new Suggestion(
                $result->valueObject->contentInfo->id,
                $content->getFieldValue('title', $language)->text,
                $result->valueObject->contentInfo->getContentType()->identifier,
                $result->valueObject->contentInfo->mainLocation->pathString,
                array_flip($result->valueObject->contentInfo->mainLocation->path)
            );
        }

        return new SuggestionCollection($collection);
    }
}
