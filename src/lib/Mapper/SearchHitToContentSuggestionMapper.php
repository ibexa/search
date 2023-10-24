<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Mapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Search\Mapper\SearchHitToContentSuggestionMapper as SearchHitToContentSuggestionMapperInterface;
use Ibexa\Contracts\Search\Provider\ParentLocationProvider as ParentLocationProviderInterface;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Search\Model\Suggestion\ContentSuggestion;

final class SearchHitToContentSuggestionMapper implements SearchHitToContentSuggestionMapperInterface
{
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

        $parentsLocation = $mainLocation->path;
        $position = array_search((string)$rootLocationId, $parentsLocation);
        if ($position !== false) {
            $parentsLocation = array_slice($parentsLocation, (int)$position);
        }

        $parentCollection = $this->parentLocationProvider->provide($parentsLocation);

        $suggestion = new ContentSuggestion(
            $searchHit->score ?? 50,
            $content->getContentType()->identifier,
            $content->getName() ?? '',
            $content->getVersionInfo()->getContentInfo()->getId(),
            implode('/', $parentsLocation),
            $parentCollection
        );

        return $suggestion;
    }
}
