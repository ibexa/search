<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Contracts\Search\EventDispatcher\Event;

use Ibexa\Contracts\Search\Event\SuggestionEvent;
use Ibexa\Contracts\Search\Model\Suggestion\SuggestionCollection;
use Ibexa\Search\Model\SuggestionQuery;
use PHPUnit\Framework\TestCase;

final class SuggestionEventTest extends TestCase
{
    public function testConstruct(): void
    {
        $suggestionEvent = new SuggestionEvent(new SuggestionQuery('test', 3));

        self::assertInstanceOf(SuggestionCollection::class, $suggestionQuery->getSuggestionCollection());
        self::assertInstanceOf(SuggestionQuery::class, $suggestionQuery->getQuery());
    }
}
