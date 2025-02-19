<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\ValueResolver;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Search\Model\SuggestionQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class SuggestionQueryArgumentResolver implements ValueResolverInterface
{
    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    /**
     * @return iterable<\Ibexa\Search\Model\SuggestionQuery>
     *
     * @throw \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->supports($argument)) {
            return [];
        }

        $defaultLimit = $this->configResolver->getParameter('search.suggestion.result_limit');
        $query = $request->query->get('query');
        $limit = $request->query->getInt('limit', $defaultLimit);
        $language = $request->query->get('language');

        if ($query === null) {
            throw new BadRequestHttpException('Missing query parameter');
        }

        yield new SuggestionQuery($query, $limit, $language);
    }

    private function supports(ArgumentMetadata $argument): bool
    {
        return SuggestionQuery::class === $argument->getType();
    }
}
