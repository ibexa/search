<?php
/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Form\Data;

class AutocompleteData
{
    /**
     * @var int
     *
     * @Assert\Range(
     *     max = 1000
     * )
     */
    private $limit;

    private ?string $query;

    public function __construct(int $limit, ?string $query)
    {
        $this->limit = $limit;
        $this->query = $query;
    }
}
