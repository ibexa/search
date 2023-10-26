<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Serializer\Normalizer\Suggestion;

use Ibexa\Contracts\Search\Model\Suggestion\ContentSuggestion;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ContentSuggestionNormalizer implements NormalizerInterface
{
    private ParentLocationCollectionNormalizer $parentLocationCollectionNormalizer;

    public function __construct(ParentLocationCollectionNormalizer $parentLocationCollectionNormalizer)
    {
        $this->parentLocationCollectionNormalizer = $parentLocationCollectionNormalizer;
    }

    /**
     * @param \Ibexa\Contracts\Search\Model\Suggestion\ContentSuggestion $object
     *
     * @return array<string, mixed>
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        return [
            'contentId' => $object->getContentId(),
            'locationId' => $object->getLocationId(),
            'contentTypeIdentifier' => $object->getContentTypeIdentifier(),
            'name' => $object->getName(),
            'pathString' => $object->getPathString(),
            'type' => 'content',
            'parentLocations' => $this->parentLocationCollectionNormalizer->normalize($object->getParentLocations()),
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof ContentSuggestion;
    }
}
