<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Serializer\Normalizer\Suggestion;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class LocationNormalizer implements NormalizerInterface
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $object
     *
     * @return array<string, mixed>
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [
            'id' => $object->getContentInfo()->getId(),
            'locationId' => $object->id,
            'name' => $object->getContent()->getName(),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Location;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Location::class => true,
        ];
    }
}
