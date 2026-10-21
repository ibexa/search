<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\SortingDefinition\Provider;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\DateModified;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Score;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinition;
use Ibexa\Search\SortingDefinition\Provider\RelevanceSortingDefinitionProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RelevanceSortingDefinitionProviderTest extends TestCase
{
    private TranslatorInterface & Stub $translator;

    protected function setUp(): void
    {
        $this->translator = self::createStub(TranslatorInterface::class);
        $this->translator->method('trans')->willReturnArgument(0);
    }

    public function testGetSortingDefinitionsWithScoringCapability(): void
    {
        $searchService = $this->createMock(SearchService::class);
        $searchService
            ->expects($this->once())
            ->method('supports')
            ->with(SearchService::CAPABILITY_SCORING)
            ->willReturn(true);

        self::assertEquals(
            [
                new SortingDefinition(
                    'relevance',
                    'sort_definition.relevance.label',
                    [
                        new Score(Query::SORT_DESC),
                    ],
                    100
                ),
            ],
            (new RelevanceSortingDefinitionProvider($searchService, $this->translator))->getSortingDefinitions()
        );
    }

    public function testGetSortingDefinitionsWithoutScoringCapability(): void
    {
        $searchService = $this->createMock(SearchService::class);
        $searchService
            ->expects($this->once())
            ->method('supports')
            ->with(SearchService::CAPABILITY_SCORING)
            ->willReturn(false);

        self::assertEquals(
            [
                new SortingDefinition(
                    'relevance',
                    'sort_definition.relevance.label',
                    [
                        new DateModified(Query::SORT_DESC),
                    ],
                    100
                ),
            ],
            (new RelevanceSortingDefinitionProvider($searchService, $this->translator))->getSortingDefinitions()
        );
    }
}
