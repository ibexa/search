<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\Mapper;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Search\Model\Suggestion\ContentSuggestion;
use Ibexa\Contracts\Search\Provider\ParentLocationProviderInterface;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Search\Mapper\SearchHitToContentSuggestionMapper;
use PHPUnit\Framework\TestCase;

final class SearchHitToContentSuggestionMapperTest extends TestCase
{
    public function testMap(): void
    {
        $mapper = new SearchHitToContentSuggestionMapper(
            $this->getParentLocationProviderMock(),
            $this->getConfigResolverMock()
        );

        $content = $this->createContentWithLocationPath(['1', '2', '3', '4', '5', '6', '7']);

        $result = $mapper->map(
            new SearchHit([
                'valueObject' => $content,
            ])
        );

        self::assertInstanceOf(ContentSuggestion::class, $result);
        self::assertSame($content, $result->getContent());
        self::assertSame('5/6/7', $result->getPathString());
        self::assertCount(3, $result->getParentsLocation());
        self::assertSame('name_eng', $result->getName());
        self::assertSame(50.0, $result->getScore());
    }

    public function testMapContentOutsideRootLocation(): void
    {
        $mapper = new SearchHitToContentSuggestionMapper(
            $this->getParentLocationProviderMock(),
            $this->getConfigResolverMock()
        );

        $content = $this->createContentWithLocationPath(['1', '2', '3', '4', '6', '7']);

        $result = $mapper->map(
            new SearchHit([
                'valueObject' => $content,
            ])
        );

        self::assertInstanceOf(ContentSuggestion::class, $result);
        self::assertSame($content, $result->getContent());
        self::assertSame('2/3/4/6/7', $result->getPathString());
        self::assertCount(5, $result->getParentsLocation());
        self::assertSame('name_eng', $result->getName());
        self::assertSame(50.0, $result->getScore());
    }

    /**
     * @param string[] $path
     */
    private function createContentWithLocationPath(array $path): Content
    {
        return new Content([
            'id' => 1,
            'contentInfo' => new ContentInfo([
                'name' => 'name',
                'mainLanguageCode' => 'eng-GB',
                'mainLocationId' => 1,
                'contentTypeId' => 1,
            ]),
            'versionInfo' => new VersionInfo([
                'initialLanguageCode' => 'eng-GB',
                'names' => ['eng-GB' => 'name_eng'],
                'contentInfo' => new ContentInfo([
                    'id' => 1,
                    'mainLanguageCode' => 'eng-GB',
                    'contentTypeId' => 1,
                    'mainLocation' => new Location([
                        'id' => 8,
                        'path' => $path,
                    ]),
                ]),
            ]),
            'contentType' => new ContentType([
                'identifier' => 'content_type_identifier',
            ]),
        ]);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface
     */
    private function getConfigResolverMock(): ConfigResolverInterface
    {
        $configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $configResolverMock->method('getParameter')->willReturn(5);

        return $configResolverMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Contracts\Search\Provider\ParentLocationProviderInterface
     */
    private function getParentLocationProviderMock(): ParentLocationProviderInterface
    {
        $configResolverMock = $this->createMock(ParentLocationProviderInterface::class);
        $configResolverMock->method('provide')->willReturnCallback(static function (array $locationIds): array {
            $locations = [];

            foreach ($locationIds as $locationId) {
                $locations[] = new Location(['id' => $locationId]);
            }

            return $locations;
        });

        return $configResolverMock;
    }
}
