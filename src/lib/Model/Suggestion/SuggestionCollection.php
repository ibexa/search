<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Model\Suggestion;

use Ibexa\Contracts\Core\Collection\MutableArrayList;

/**
 * @template-extends \Ibexa\Contracts\Core\Collection\MutableArrayList<
 *     \Ibexa\Search\Model\Suggestion\ContentSuggestion
 * >
 */
final class SuggestionCollection extends MutableArrayList
{
}
