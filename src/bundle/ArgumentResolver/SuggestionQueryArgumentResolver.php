<?php
/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\ArgumentResolver;

use Ibexa\Search\Model\SuggestionQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class SuggestionQueryArgumentResolver implements ArgumentValueResolverInterface
{
    private int $defaultLimit;

    public function __construct(int $defaultLimit)
    {
        $this->defaultLimit = $defaultLimit;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return SuggestionQuery::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $query = $request->get('query');
        $limit = $request->query->getInt('limit', $this->defaultLimit);
        $language = $request->get('language');

        yield new SuggestionQuery($query, $limit, $language);
    }
}
