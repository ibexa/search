<?php

namespace Ibexa\Contracts\Search\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UrlGeneratorEvent extends Event
{
    private int $contentId;
    private int $locationId;
    private string $languageCode;
    private string $url;

    public function __construct(int $content, int $locationId, string $languageCode)
    {
        $this->contentId = $content;
        $this->locationId = $locationId;
        $this->languageCode = $languageCode;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getContentId(): int
    {
        return $this->contentId;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }
}