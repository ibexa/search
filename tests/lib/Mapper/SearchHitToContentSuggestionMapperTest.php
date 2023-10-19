<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Search\Mapper;

use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Persistence\Content\Location;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Search\Mapper\SearchHitToContentSuggestionMapper;
use Ibexa\Search\Model\Suggestion\ContentSuggestion;
use PHPUnit\Framework\TestCase;

final class SearchHitToContentSuggestionMapperTest extends TestCase
{
    public function testMap(): void
    {
        $this->markTestIncomplete('This test has not been implemented yet.');

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
                        'mainLocation' => new Location([
                            'pathString' => [1, 2, 3],
                        ]),
                        'contentTypeId' => 1,
                    ]),
                    'versionInfo' => new VersionInfo([
                        'contentInfo' => new ContentInfo([
                            'name' => 'name',
                            'mainLanguageCode' => 'eng-GB',
                            'contentTypeId' => 1,
                        ]),
                    ]),
                    'contentType' => new ContentType([
                        'identifier' => 'content_type_identifier',
                    ]),
                ]),
            ])
        );
        $this->assertInstanceOf(ContentSuggestion::class, $result);
    }

    private function getConfigResolverMock(): ConfigResolverInterface
    {
        $configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $configResolverMock->method('getParameter')->willReturn(5);

        return $configResolverMock;
    }
}
