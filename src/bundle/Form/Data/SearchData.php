<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Form\Data;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SearchData
{
    #[Assert\Range(max: 1000)]
    private int $limit;

    private int $page;

    private ?string $query;

    private ?Section $section;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType[] */
    private array $contentTypes;

    private array $lastModified;

    private array $created;

    private ?User $creator;

    private ?string $subtree;

    private ?Language $searchLanguage;

    private ?SearchUsersData $searchUsersData;

    private ?SortingDefinitionInterface $sortingDefinition;

    public function __construct(
        int $limit = 10,
        int $page = 1,
        ?string $query = null,
        ?Section $section = null,
        array $contentTypes = [],
        array $lastModified = [],
        array $created = [],
        ?User $creator = null,
        ?string $subtree = null,
        ?Language $searchLanguage = null,
        ?SearchUsersData $searchUsersData = null,
        ?SortingDefinitionInterface $sortingDefinition = null
    ) {
        $this->limit = $limit;
        $this->page = $page;
        $this->query = $query;
        $this->section = $section;
        $this->contentTypes = $contentTypes;
        $this->lastModified = $lastModified;
        $this->created = $created;
        $this->creator = $creator;
        $this->subtree = $subtree;
        $this->searchLanguage = $searchLanguage;
        $this->searchUsersData = $searchUsersData;
        $this->sortingDefinition = $sortingDefinition;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function setQuery(?string $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function setContentTypes(array $contentTypes): void
    {
        $this->contentTypes = $contentTypes;
    }

    public function setLastModified(array $lastModified): void
    {
        $this->lastModified = $lastModified;
    }

    public function setCreated(array $created): void
    {
        $this->created = $created;
    }

    public function setCreator(User $creator): void
    {
        $this->creator = $creator;
    }

    public function setSubtree(?string $subtree): void
    {
        $this->subtree = $subtree;
    }

    public function setSearchUsersData(?SearchUsersData $searchUsersData): void
    {
        $this->searchUsersData = $searchUsersData;
    }

    public function setSearchLanguage(?Language $searchLanguage): void
    {
        $this->searchLanguage = $searchLanguage;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function getContentTypes(): array
    {
        return $this->contentTypes;
    }

    public function getLastModified(): array
    {
        return $this->lastModified;
    }

    public function getCreated(): array
    {
        return $this->created;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function getSubtree(): ?string
    {
        return $this->subtree;
    }

    public function getSearchLanguage(): ?Language
    {
        return $this->searchLanguage;
    }

    public function getSearchUsersData(): ?SearchUsersData
    {
        return $this->searchUsersData;
    }

    public function getSortingDefinition(): ?SortingDefinitionInterface
    {
        return $this->sortingDefinition;
    }

    public function setSortingDefinition(?SortingDefinitionInterface $sortingDefinition): void
    {
        $this->sortingDefinition = $sortingDefinition;
    }

    public function isFiltered(): bool
    {
        $contentTypes = $this->getContentTypes();
        $section = $this->getSection();
        $lastModified = $this->getLastModified();
        $created = $this->getCreated();
        $creator = $this->getCreator();
        $subtree = $this->getSubtree();

        return
            !empty($contentTypes) ||
            null !== $section ||
            !empty($lastModified) ||
            !empty($created) ||
            $creator instanceof User ||
            null !== $subtree;
    }
}
