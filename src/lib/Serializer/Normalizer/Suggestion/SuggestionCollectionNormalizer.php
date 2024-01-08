<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Serializer\Normalizer\Suggestion;

use Ibexa\Contracts\Search\Model\Suggestion\SuggestionCollection;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SuggestionCollectionNormalizer implements
    NormalizerInterface,
    NormalizerAwareInterface,
    CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    /**
     * @param \Ibexa\Contracts\Search\Model\Suggestion\SuggestionCollection $object
     * @param array<string, mixed> $context
     *
     * @return array<string,mixed>.
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $suggestionCollection = [];

        foreach ($object as $parentLocation) {
            $suggestionCollection[] = $this->normalizer->normalize($parentLocation, $format, $context);
        }

        return [
            'suggestionResults' => $suggestionCollection,
            'totalCount' => $object->getTotalCount(),
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof SuggestionCollection;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
