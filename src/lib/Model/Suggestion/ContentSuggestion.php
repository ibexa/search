<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Model\Suggestion;

final class ContentSuggestion extends Suggestion
{
    private int $contentId;

    private string $contentTypeIdentifier;

    /**
     * @param array<int|string, int|string> $parentLocation
     */
    public function __construct(
        float $score,
        string $contentTypeIdentifier,
        string $name,
        int $contentId,
        string $pathString = '',
        array $parentLocation = []
    ) {
        parent::__construct($score, $name, $pathString, $parentLocation);
        $this->contentId = $contentId;
        $this->contentTypeIdentifier = $contentTypeIdentifier;
    }

    public function getContentId(): int
    {
        return $this->contentId;
    }

    public function getContentTypeIdentifier(): string
    {
        return $this->contentTypeIdentifier;
    }

    public function getType(): string
    {
        return 'content';
    }
}