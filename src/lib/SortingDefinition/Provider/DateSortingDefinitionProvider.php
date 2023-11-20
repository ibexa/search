<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\SortingDefinition\Provider;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\DateModified;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\DatePublished;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinition;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionProviderInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DateSortingDefinitionProvider implements SortingDefinitionProviderInterface, TranslationContainerInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getSortingDefinitions(): array
    {
        return [
            new SortingDefinition(
                'date_published_asc',
                $this->getLabel('date_published_asc'),
                [
                    new DatePublished(Query::SORT_ASC),
                ],
                400
            ),
            new SortingDefinition(
                'date_published_desc',
                $this->getLabel('date_published_desc'),
                [
                    new DatePublished(Query::SORT_DESC),
                ],
                500
            ),
            new SortingDefinition(
                'date_modified_asc',
                $this->getLabel('date_modified_asc'),
                [
                    new DateModified(Query::SORT_ASC),
                ],
                600
            ),
            new SortingDefinition(
                'date_modified_desc',
                $this->getLabel('date_modified_desc'),
                [
                    new DateModified(Query::SORT_DESC),
                ],
                700
            ),
        ];
    }

    private function getLabel(string $identifier): string
    {
        return $this->translator->trans(
            /** @Ignore */
            sprintf('sort_definition.%s.label', $identifier),
            [],
            'ibexa_search'
        );
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('sort_definition.date_published_asc.label', 'ibexa_search'))->setDesc('Sort by publication date (Oldest)'),
            (new Message('sort_definition.date_published_desc.label', 'ibexa_search'))->setDesc('Sort by publication date (Newest)'),
            (new Message('sort_definition.date_modified_asc.label', 'ibexa_search'))->setDesc('Sort by modification date (Oldest)'),
            (new Message('sort_definition.date_modified_desc.label', 'ibexa_search'))->setDesc('Sort by modification date (Newest)'),
        ];
    }
}
