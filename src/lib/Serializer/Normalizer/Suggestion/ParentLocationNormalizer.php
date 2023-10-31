<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Serializer\Normalizer\Suggestion;

use Ibexa\Contracts\Search\Model\Suggestion\ParentLocation;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ParentLocationNormalizer implements NormalizerInterface
{
    /**
     * @param \Ibexa\Contracts\Search\Model\Suggestion\ParentLocation $object
     *
     * @return array<string, mixed>
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        return [
            'contentId' => $object->getContentId(),
            'locationId' => $object->getLocationId(),
            'name' => $object->getName(),
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof ParentLocation;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === static::class;
    }
}
