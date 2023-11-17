<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\Service\Decorator;

use Ibexa\Contracts\Search\Model\Suggestion\SuggestionCollection;
use Ibexa\Contracts\Search\Service\SuggestionServiceInterface;
use Ibexa\Search\Model\SuggestionQuery;

abstract class SuggestionServiceDecorator implements SuggestionServiceInterface
{
    protected SuggestionServiceInterface $innerService;

    public function __construct(SuggestionServiceInterface $innerService)
    {
        $this->innerService = $innerService;
    }

    public function suggest(SuggestionQuery $query): SuggestionCollection
    {
        return $this->innerService->suggest($query);
    }
}
