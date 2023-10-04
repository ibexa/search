<?php
/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Controller;

use Ibexa\Search\Service\AutocompleteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final class AutoCompleteController extends AbstractController
{
    private AutocompleteService $autocompleteService;
    private SerializerInterface $serializer;

    public function __construct(
        SerializerInterface $serializer,
        AutocompleteService $autocompleteService
    ) {
        $this->autocompleteService = $autocompleteService;
        $this->serializer = $serializer;
    }

    public function autocompleteAction(string $search, int $limit): Response
    {
        $result = $this->autocompleteService->suggest($search);

        // @todo object
        $serializedResults = $this->serializer->serialize($result, $limit);


        return new Response($serializedResults, 200);
    }
}
