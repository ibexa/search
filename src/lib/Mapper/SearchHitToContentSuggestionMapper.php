<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Mapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Search\Mapper\SearchHitToContentSuggestionMapperInterface;
use Ibexa\Contracts\Search\Model\Suggestion\ContentSuggestion;
use Ibexa\Contracts\Search\Provider\ParentLocationProviderInterface;

final class SearchHitToContentSuggestionMapper implements SearchHitToContentSuggestionMapperInterface
{
    private const ROOT_LOCATION_ID = 1;

    private ConfigResolverInterface $configResolver;

    private ParentLocationProviderInterface $parentLocationProvider;

    public function __construct(
        ParentLocationProviderInterface $parentLocationProvider,
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
        $this->parentLocationProvider = $parentLocationProvider;
    }

    public function map(SearchHit $searchHit): ?ContentSuggestion
    {
        $content = $searchHit->valueObject;

        if (!$content instanceof Content) {
            return null;
        }

        $rootLocationId = $this->configResolver->getParameter('content.tree_root.location_id');

        $mainLocation = $content->getVersionInfo()->getContentInfo()->getMainLocation();

        if ($mainLocation === null) {
            return null;
        }

        $parentsLocation = array_map('intval', $mainLocation->path);
        $position = array_search($rootLocationId, $parentsLocation, true);
        if ($position !== false) {
            $parentsLocation = array_slice($parentsLocation, (int)$position);
        }

        if (reset($parentsLocation) === self::ROOT_LOCATION_ID) {
            // Remove "Top Level Nodes" from suggestion path
            array_shift($parentsLocation);
        }

        $parentLocations = $this->parentLocationProvider->provide($parentsLocation);

        return new ContentSuggestion(
            $searchHit->score ?? 50.0,
            $content,
            $content->getContentType(),
            implode('/', $parentsLocation),
            $parentLocations
        );
    }
}
