<?php
/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Controller;

use Ibexa\Search\Service\SearchAutoCompleteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final class SearchAutoCompleteController extends AbstractController
{
    private SearchAutoCompleteService $autoCompleteService;
    private SerializerInterface $serializer;

    public function __construct(
        SerializerInterface $serializer,
        SearchAutoCompleteService $autocompleteService
    ) {
        $this->autoCompleteService = $autocompleteService;
        $this->serializer = $serializer;
    }

    public function suggestAction(Request $request): Response
    {
        $search = $request->get('search');
        $language = $request->get('language');
        $limit = (int) $request->get('limit');

        $result = $this->autoCompleteService->suggest($search, $limit, $language);

        $serializedResults = $this->serializer->serialize($result, 'json');

        return new Response($serializedResults, 200);
    }
}
