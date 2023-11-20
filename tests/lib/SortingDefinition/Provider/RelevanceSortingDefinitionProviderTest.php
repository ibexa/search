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
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RelevanceSortingDefinitionProviderTest extends TestCase
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface&\PHPUnit\Framework\MockObject\MockObject) */
    private TranslatorInterface $translator;

    /** @var \Ibexa\Contracts\Core\Repository\SearchService&\PHPUnit\Framework\MockObject\MockObject */
    private SearchService $searchService;

    private RelevanceSortingDefinitionProvider $provider;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->translator->method('trans')->willReturnArgument(0);

        $this->searchService = $this->createMock(SearchService::class);

        $this->provider = new RelevanceSortingDefinitionProvider(
            $this->searchService,
            $this->translator
        );
    }

    public function testGetSortingDefinitionsWithScoringCapability(): void
    {
        $this->searchService
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
            $this->provider->getSortingDefinitions()
        );
    }

    public function testGetSortingDefinitionsWithoutScoringCapability(): void
    {
        $this->searchService
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
            $this->provider->getSortingDefinitions()
        );
    }
}
