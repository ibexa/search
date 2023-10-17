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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

final class SuggestionController extends AbstractController
{
    private SuggestionService $suggestionService;

    private SerializerInterface $serializer;

    public function __construct(
        SerializerInterface $serializer,
        SuggestionService $suggestionService
    ) {
        $this->suggestionService = $suggestionService;
        $this->serializer = $serializer;
    }

    public function suggestAction(Request $request): JsonResponse
    {
        $query = $request->get('query');
        $limit = (int) $request->get('limit');
        $language = $request->get('language');

        $suggestionQuery = new SuggestionQuery($query, $limit, $language);
        $result = $this->suggestionService->suggest($suggestionQuery);

        $serializedResults = $this->serializer->serialize($result, 'json');

        return (new JsonResponse(null, 200))->setJson($serializedResults);
    }
}
