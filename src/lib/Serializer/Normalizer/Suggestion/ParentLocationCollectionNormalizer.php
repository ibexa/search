<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Serializer\Normalizer\Suggestion;

use Ibexa\Contracts\Search\Model\Suggestion\ParentLocationCollection;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ParentLocationCollectionNormalizer implements
    NormalizerInterface,
    NormalizerAwareInterface,
    CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    /**
     * @param \Ibexa\Contracts\Search\Model\Suggestion\ParentLocationCollection $object
     * @param array<string, mixed> $context
     *
     * @return array<int,mixed>.
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $normalizedData = [];

        foreach ($object as $parentLocation) {
            $normalizedData[] = $this->normalizer->normalize($parentLocation, $format, $context);
        }

        return $normalizedData;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof ParentLocationCollection;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
