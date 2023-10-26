<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;

final class SuggestionSerializerFactory
{
    /**
     * @template T of \Symfony\Component\Serializer\Normalizer\DenormalizerInterface
     * |\Symfony\Component\Serializer\Normalizer\NormalizerInterface
     * @param iterable<T> $normalizers
     */
    public function __invoke(iterable $normalizers): SuggestionSerializer
    {
        $encoders = [new JsonEncoder()];
        $normalizersArray = is_array($normalizers) ? $normalizers : iterator_to_array($normalizers);

        return new SuggestionSerializer($normalizersArray, $encoders);
    }
}
