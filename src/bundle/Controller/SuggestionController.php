<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Controller;

use Ibexa\Search\Model\SuggestionQuery;
use Ibexa\Search\Service\SuggestionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

final class SuggestionController extends AbstractController
{
    private SuggestionService $suggestionService;

    public function __construct(
        SuggestionService $suggestionService
    ) {
        $this->suggestionService = $suggestionService;
    }

    public function suggestAction(SuggestionQuery $suggestionQuery): JsonResponse
    {
        $result = $this->suggestionService->suggest($suggestionQuery);

        return $this->json($result);
    }
}
