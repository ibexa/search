<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Search\Request\ParamConverter;

use Ibexa\Bundle\Search\Request\ParamConverter\SuggestionQueryParamConverter;
use Ibexa\Search\Model\SuggestionQuery;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

final class SuggestionQueryParamConverterTest extends TestCase
{
    private const DEFAULT_LIMIT = 10;

    private SuggestionQueryParamConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new SuggestionQueryParamConverter(self::DEFAULT_LIMIT);
    }

    /**
     * @dataProvider provideSupportsTestData
     */
    public function testSupports(string $class, bool $expectedResult): void
    {
        $configuration = new ParamConverter([]);
        $configuration->setClass($class);

        $this->assertSame($expectedResult, $this->converter->supports($configuration));
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public function provideSupportsTestData(): array
    {
        return [
            'Supports SuggestionQuery' => [SuggestionQuery::class, true],
            'Does not support other classes' => [\stdClass::class, false],
        ];
    }

    /**
     * @dataProvider provideApplyTestData
     *
     * @param array{query: string, limit?: int, language?: string|null} $requestData
     */
    public function testApply(array $requestData, SuggestionQuery $expectedSuggestionQuery): void
    {
        $configuration = new ParamConverter([]);
        $configuration->setName('suggestion');
        $configuration->setClass(SuggestionQuery::class);

        $request = new Request([], [], [], [], [], []);
        $request->query->add($requestData);

        $this->converter->apply($request, $configuration);

        /** @var \Ibexa\Search\Model\SuggestionQuery $suggestionQuery */
        $suggestionQuery = $request->attributes->get('suggestion');

        $this->assertInstanceOf(SuggestionQuery::class, $suggestionQuery);
        $this->assertEquals($expectedSuggestionQuery, $suggestionQuery);
    }

    /**
     * @return array<string, array{array{query: string, limit?: int, language?: string|null}, SuggestionQuery}>
     */
    public function provideApplyTestData(): array
    {
        return [
            'All parameters provided' => [
                ['query' => 'test', 'limit' => 5, 'language' => 'en'],
                new SuggestionQuery('test', 5, 'en'),
            ],
            'Only query provided' => [
                ['query' => 'test'],
                new SuggestionQuery('test', self::DEFAULT_LIMIT, null),
            ],
            'Limit falls back to default' => [
                ['query' => 'test', 'language' => 'en'],
                new SuggestionQuery('test', self::DEFAULT_LIMIT, 'en'),
            ],
        ];
    }
}
