<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Request\ParamConverter;

use Ibexa\Search\Model\SuggestionQuery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

final class SuggestionQueryParamConverter implements ParamConverterInterface
{
    private int $defaultLimit;

    public function __construct(int $defaultLimit)
    {
        $this->defaultLimit = $defaultLimit;
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $query = $request->get('query');
        $limit = $request->query->getInt('limit', $this->defaultLimit);
        $language = $request->get('language');

        $suggestionQuery = new SuggestionQuery($query, $limit, $language);

        $request->attributes->set($configuration->getName(), $suggestionQuery);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return SuggestionQuery::class === $configuration->getClass();
    }
}
