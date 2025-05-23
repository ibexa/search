<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\Mapper;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Search\Mapper\PagerSearchDataMapper;
use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use Ibexa\Core\Repository\LocationResolver\LocationResolver;
use Pagerfanta\Pagerfanta;

/**
 * @phpstan-type TData = array{
 *   content: \Ibexa\Contracts\Core\Repository\Values\Content\Content,
 *   contentTypeId: int,
 *   contentId: int,
 *   name: string,
 *   language: string,
 *   contributor: \Ibexa\Contracts\Core\Repository\Values\User\User|null,
 *   version: int,
 *   content_type: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType,
 *   modified: \DateTimeInterface,
 *   initialLanguageCode: string,
 *   content_is_user: bool,
 *   available_enabled_translations: iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Language>,
 *   available_translations: iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Language>,
 *   translation_language_code: string,
 *   resolvedLocation: \Ibexa\Contracts\Core\Repository\Values\Content\Location
 * }
 *
 * @phpstan-implements PagerSearchDataMapper<TData>
 */
final class PagerSearchContentToDataMapper implements PagerSearchDataMapper
{
    private ContentTypeService $contentTypeService;

    private UserService $userService;

    private UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider;

    private TranslationHelper $translationHelper;

    private LanguageService $languageService;

    private LocationResolver $locationResolver;

    public function __construct(
        ContentTypeService $contentTypeService,
        UserService $userService,
        UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider,
        TranslationHelper $translationHelper,
        LanguageService $languageService,
        LocationResolver $locationResolver
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->userService = $userService;
        $this->userLanguagePreferenceProvider = $userLanguagePreferenceProvider;
        $this->translationHelper = $translationHelper;
        $this->languageService = $languageService;
        $this->locationResolver = $locationResolver;
    }

    /**
     * @param Pagerfanta<\Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit<\Ibexa\Contracts\Core\Repository\Values\Content\Content>> $pager
     */
    public function map(Pagerfanta $pager): array
    {
        $data = [];
        $contentTypeIds = [];

        foreach ($pager as $searchHit) {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
            $content = $searchHit->valueObject;
            $contentInfo = $content->contentInfo;

            $contentTypeIds[] = $contentInfo->contentTypeId;
            $data[] = [
                'content' => $content,
                'contentTypeId' => $contentInfo->contentTypeId,
                'contentId' => $content->id,
                'mainLocationId' => $contentInfo->mainLocationId,
                'name' => $this->translationHelper->getTranslatedContentName(
                    $content,
                    $searchHit->matchedTranslation
                ),
                'language' => $contentInfo->mainLanguageCode,
                'contributor' => $this->getContributor($contentInfo),
                'version' => $content->versionInfo->versionNo,
                'content_type' => $content->getContentType(),
                'modified' => $content->versionInfo->modificationDate,
                'initialLanguageCode' => $content->versionInfo->initialLanguageCode,
                'content_is_user' => $this->isContentIsUser($content),
                'available_enabled_translations' => $this->getAvailableTranslations($content, true),
                'available_translations' => $this->getAvailableTranslations($content),
                'translation_language_code' => $searchHit->matchedTranslation,
                'resolvedLocation' => $this->locationResolver->resolveLocation($contentInfo),
            ];
        }

        $this->setTranslatedContentTypesNames($data, $contentTypeIds);

        return $data;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param bool $filterDisabled
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Language[]
     */
    private function getAvailableTranslations(
        Content $content,
        bool $filterDisabled = false
    ): iterable {
        $availableTranslationsLanguages = $this->languageService->loadLanguageListByCode(
            $content->versionInfo->languageCodes
        );

        if (false === $filterDisabled) {
            return $availableTranslationsLanguages;
        }

        return array_filter(
            $availableTranslationsLanguages,
            (static function (Language $language): bool {
                return $language->enabled;
            })
        );
    }

    private function isContentIsUser(Content $content): bool
    {
        return $this->userService->isUser($content);
    }

    private function getContributor(ContentInfo $contentInfo): ?User
    {
        try {
            return $this->userService->loadUser($contentInfo->ownerId);
        } catch (NotFoundException $e) {
            return null;
        }
    }

    /**
     * @phpstan-param TData[] $data
     * @phpstan-param int[] $contentTypeIds
     */
    private function setTranslatedContentTypesNames(array &$data, array $contentTypeIds): void
    {
        // load list of content types with proper translated names
        $contentTypes = iterator_to_array(
            $this->contentTypeService->loadContentTypeList(
                array_unique($contentTypeIds),
                $this->userLanguagePreferenceProvider->getPreferredLanguages()
            )
        );

        foreach ($data as $idx => $item) {
            // get content type from bulk-loaded list or fallback to lazy loaded one if not present
            $contentTypeId = $item['contentTypeId'];
            /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType */
            $contentType = $contentTypes[$contentTypeId] ?? $item['content']->getContentType();

            $data[$idx]['type'] = $contentType->getName();
            unset($data[$idx]['content'], $data[$idx]['contentTypeId']);
        }
    }
}
