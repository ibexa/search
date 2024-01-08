<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\Serializer\Normalizer\Suggestion;

use Ibexa\Contracts\Search\Model\Suggestion\Suggestion;
use Ibexa\Contracts\Search\Model\Suggestion\SuggestionCollection;
use Ibexa\Search\Serializer\Normalizer\Suggestion\SuggestionCollectionNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SuggestionCollectionNormalizerTest extends TestCase
{
    /** @var \Symfony\Component\Serializer\Normalizer\NormalizerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private NormalizerInterface $normalizer;

    private SuggestionCollectionNormalizer $suggestionCollectionNormalizer;

    protected function setUp(): void
    {
        $this->normalizer = $this->createMock(NormalizerInterface::class);
        $this->suggestionCollectionNormalizer = new SuggestionCollectionNormalizer();
        $this->suggestionCollectionNormalizer->setNormalizer($this->normalizer);
    }

    public function testNormalize(): void
    {
        $suggestionItemMock = $this->createMock(Suggestion::class);
        $suggestionCollection = new SuggestionCollection([$suggestionItemMock]);
        $suggestionCollection->increaseTotalCount(100);

        $this->normalizer
            ->expects(self::once())
            ->method('normalize')
            ->with($suggestionItemMock)
            ->willReturn(['mocked_normalized_data']);

        $expected = [
            'suggestionResults' => [['mocked_normalized_data']],
            'totalCount' => 100,
        ];

        self::assertEquals(
            $expected,
            $this->suggestionCollectionNormalizer->normalize($suggestionCollection)
        );
    }
}
