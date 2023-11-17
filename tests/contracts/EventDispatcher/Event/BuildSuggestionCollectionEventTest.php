<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Contracts\Search\EventDispatcher\Event;

use Ibexa\Contracts\Search\Event\BuildSuggestionCollectionEvent;
use Ibexa\Search\Model\SuggestionQuery;
use PHPUnit\Framework\TestCase;

final class BuildSuggestionCollectionEventTest extends TestCase
{
    public function testConstruct(): void
    {
        $suggestionQuery = new SuggestionQuery('test', 3);
        $suggestionEvent = new BuildSuggestionCollectionEvent($suggestionQuery);

        self::assertCount(0, $suggestionEvent->getSuggestionCollection());
        self::assertSame($suggestionQuery, $suggestionEvent->getQuery());
    }
}
