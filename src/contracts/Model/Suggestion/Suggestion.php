<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Search\Model\Suggestion;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

abstract class Suggestion extends ValueObject
{
    private float $score;

    private ?string $name;

    public function __construct(
        float $score,
        string $name = null
    ) {
        $this->score = $score;
        $this->name = $name;

        parent::__construct();
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
