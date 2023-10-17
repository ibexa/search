<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Mapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Search\Model\Suggestion\ContentSuggestion;

final class SearchHitToContentSuggestionMapper
{
    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function map(SearchHit $searchHit): ContentSuggestion
    {
        $content = $searchHit->getValueObject();

        $rootLocationId = $this->configResolver->getParameter('content.tree_root.location_id');

        $parentsLocation = $content->contentInfo->mainLocation->path;
        $position = array_search((string)$rootLocationId, $parentsLocation);
        if ($position !== false) {
            $parentsLocation = array_slice($parentsLocation, $position);
        }

        return new ContentSuggestion(
            $content->contentInfo->id,
            $content->contentInfo->getContentType()->identifier,
            $content->getFieldValue('title')->text,
            implode('/', $parentsLocation),
            array_flip($parentsLocation)
        );
    }
}
