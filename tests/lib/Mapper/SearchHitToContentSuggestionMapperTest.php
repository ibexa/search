<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\Mapper;

use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Search\Mapper\SearchHitToContentSuggestionMapper;
use Ibexa\Search\Model\Suggestion\ContentSuggestion;
use PHPUnit\Framework\TestCase;

final class SearchHitToContentSuggestionMapperTest extends TestCase
{
    public function testMap(): void
    {
        $mapper = new SearchHitToContentSuggestionMapper(
            $this->getConfigResolverMock()
        );

        $result = $mapper->map(
            new SearchHit([
                'valueObject' => new Content([
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
                        'contentInfo' => new \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo([
                            'id' => 1,
                            'mainLanguageCode' => 'eng-GB',
                            'contentTypeId' => 1,
                            'mainLocation' => new Location([
                                'path' => [1, 2, 3, 4, 5, 6, 7],
                            ]),
                        ]),
                    ]),
                    'contentType' => new ContentType([
                        'identifier' => 'content_type_identifier',
                    ]),
                ]),
            ])
        );

        $this->assertInstanceOf(ContentSuggestion::class, $result);

        $this->assertSame($result->getContentId(), 1);
        $this->assertSame($result->getParentsLocation(), [6 => '', 7 => '']);
        $this->assertSame($result->getPathString(), '6/7');
        $this->assertSame($result->getName(), 'name_eng');
        $this->assertSame($result->getScore(), 50.0);
    }

    private function getConfigResolverMock(): ConfigResolverInterface
    {
        $configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $configResolverMock->method('getParameter')->willReturn(5);

        return $configResolverMock;
    }
}
