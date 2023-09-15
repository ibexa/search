<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\SortingDefinition\Provider;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\DateModified;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\DatePublished;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinition;
use Ibexa\Search\SortingDefinition\Provider\DateSortingDefinitionProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DateSortingDefinitionProviderTest extends TestCase
{
    public function testGetSortingDefinitions(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        $provider = new DateSortingDefinitionProvider($translator);

        self::assertEquals(
            [
                new SortingDefinition(
                    'date_published_asc',
                    'sort_definition.date_published_asc.label',
                    [
                        new DatePublished(Query::SORT_ASC),
                    ],
                    400
                ),
                new SortingDefinition(
                    'date_published_desc',
                    'sort_definition.date_published_desc.label',
                    [
                        new DatePublished(Query::SORT_DESC),
                    ],
                    500
                ),
                new SortingDefinition(
                    'date_modified_asc',
                    'sort_definition.date_modified_asc.label',
                    [
                        new DateModified(Query::SORT_ASC),
                    ],
                    600
                ),
                new SortingDefinition(
                    'date_modified_desc',
                    'sort_definition.date_modified_desc.label',
                    [
                        new DateModified(Query::SORT_DESC),
                    ],
                    700
                ),
            ],
            $provider->getSortingDefinitions()
        );
    }
}
