<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Form\ChoiceLoader;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

class ContentTypeChoiceLoader implements ChoiceLoaderInterface
{
    public function __construct(
        protected ContentTypeService $contentTypeService,
        private readonly UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider
    ) {
    }

    /**
     * @return array<string, iterable<\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType>>
     */
    public function getChoiceList(): array
    {
        $contentTypesList = [];
        $preferredLanguages = $this->userLanguagePreferenceProvider->getPreferredLanguages();
        $contentTypeGroups = $this->contentTypeService->loadContentTypeGroups($preferredLanguages);
        foreach ($contentTypeGroups as $contentTypeGroup) {
            $contentTypes = $this->contentTypeService->loadContentTypes($contentTypeGroup, $preferredLanguages);
            usort($contentTypes, static function (ContentType $contentType1, ContentType $contentType2): int {
                return strnatcasecmp($contentType1->getName(), $contentType2->getName());
            });

            $contentTypesList[$contentTypeGroup->identifier] = $contentTypes;
        }

        return $contentTypesList;
    }

    public function loadChoiceList(callable $value = null): ChoiceListInterface
    {
        $choices = $this->getChoiceList();

        return new ArrayChoiceList($choices, $value);
    }

    public function loadChoicesForValues(array $values, callable $value = null): array
    {
        // Optimize
        $values = array_filter($values);
        if (empty($values)) {
            return [];
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    public function loadValuesForChoices(array $choices, callable $value = null): array
    {
        // Optimize
        $choices = array_filter($choices);
        if (empty($choices)) {
            return [];
        }

        // If no callable is set, choices are the same as values
        if (null === $value) {
            return $choices;
        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }
}
