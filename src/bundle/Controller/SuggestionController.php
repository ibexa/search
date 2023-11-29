<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Controller;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Search\Model\SuggestionQuery;
use Ibexa\Search\Service\SuggestionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

final class SuggestionController extends AbstractController
{
    private SuggestionService $suggestionService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        SuggestionService $suggestionService,
        ConfigResolverInterface $configResolver
    ) {
        $this->suggestionService = $suggestionService;
        $this->configResolver = $configResolver;
    }

    public function suggestAction(SuggestionQuery $suggestionQuery): JsonResponse
    {
        $minQueryLength = $this->configResolver->getParameter('search.suggestion.min_query_length');
        if (mb_strlen($suggestionQuery->getQuery()) < $minQueryLength) {
            return $this->json([]);
        }

        $result = $this->suggestionService->suggest($suggestionQuery);

        return $this->json($result);
    }
}
