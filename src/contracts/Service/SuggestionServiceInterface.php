<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\Service;

use Ibexa\Contracts\Search\Model\Suggestion\SuggestionCollection;
use Ibexa\Search\Model\SuggestionQuery;

interface SuggestionServiceInterface
{
    public function suggest(SuggestionQuery $query): SuggestionCollection;
}
