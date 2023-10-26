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
     * @param array<
     *     \Symfony\Component\Serializer\Normalizer\DenormalizerInterface|\Symfony\Component\Serializer\Normalizer\NormalizerInterface
     * > $normalizers
     */
    public function __invoke(array $normalizers): SuggestionSerializer
    {
        $encoders = [new JsonEncoder()];

        return new SuggestionSerializer($normalizers, $encoders);
    }
}
