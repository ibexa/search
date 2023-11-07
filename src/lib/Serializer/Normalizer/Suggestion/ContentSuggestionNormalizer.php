<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Serializer\Normalizer\Suggestion;

use Ibexa\Contracts\Search\Model\Suggestion\ContentSuggestion;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ContentSuggestionNormalizer implements
    NormalizerInterface,
    NormalizerAwareInterface,
    CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    /**
     * @param \Ibexa\Contracts\Search\Model\Suggestion\ContentSuggestion $object
     *
     * @return array<string, mixed>
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $content = $object->getContent();

        return [
            'contentId' => $content->id,
            'locationId' => $content->getVersionInfo()->getContentInfo()->getMainLocation()->id ?? null,
            'contentTypeIdentifier' => $object->getContentType()->identifier,
            'name' => $object->getName(),
            'pathString' => $object->getPathString(),
            'type' => 'content',
            'parentLocations' => $this->normalizer->normalize($object->getParentsLocation()),
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof ContentSuggestion;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
