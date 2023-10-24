<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Serializer\Normalizer;

use Ibexa\Search\Model\Suggestion\ContentSuggestion;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

final class ContentSuggestionNormalizer implements ContextAwareNormalizerInterface
{
    /**
     * @param \Ibexa\Search\Model\Suggestion\ContentSuggestion $object
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $data = [
            'name' => $object->getName(),
            'type' => $object->getType(),
            'score' => $object->getScore(),
            'pathString' => $object->getPathString(),
            'contentId' => $object->getContentId(),
            'parentsLocation' => $object->getParentLocations(),
            'contentTypeIdentifier' => $object->getContentTypeIdentifier(),
        ];

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof ContentSuggestion;
    }
}
