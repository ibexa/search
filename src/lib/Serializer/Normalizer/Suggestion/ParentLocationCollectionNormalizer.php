<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Serializer\Normalizer\Suggestion;

use Ibexa\Contracts\Search\Model\Suggestion\ParentLocationCollection;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ParentLocationCollectionNormalizer implements NormalizerInterface
{
    private ParentLocationNormalizer $parentLocationNormalizer;

    public function __construct(ParentLocationNormalizer $parentLocationNormalizer)
    {
        $this->parentLocationNormalizer = $parentLocationNormalizer;
    }

    /**
     * @param \Ibexa\Contracts\Search\Model\Suggestion\ParentLocationCollection $object
     *
     * @return array<int<0, max>, array<string, mixed>>
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $normalizedData = [];

        foreach ($object as $parentLocation) {
            $normalizedData[] = $this->parentLocationNormalizer->normalize($parentLocation);
        }

        return $normalizedData;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof ParentLocationCollection;
    }
}
