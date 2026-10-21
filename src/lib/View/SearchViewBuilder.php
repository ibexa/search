<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\View;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Search\Mapper\PagerSearchDataMapper;
use Ibexa\Core\MVC\Symfony\View\Builder\ViewBuilder;
use Ibexa\Core\MVC\Symfony\View\Configurator;
use Ibexa\Core\MVC\Symfony\View\ParametersInjector;
use Ibexa\Core\Pagination\Pagerfanta\ContentSearchHitAdapter;
use Ibexa\Core\QueryType\QueryType;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormInterface;

class SearchViewBuilder implements ViewBuilder
{
    /**
     * @param PagerSearchDataMapper<array{
     *    content: Content,
     *    contentTypeId: int,
     *    contentId: int,
     *    name: string,
     *    language: string,
     *    contributor: User|null,
     *    version: int,
     *    content_type: ContentType,
     *    modified: \DateTimeInterface,
     *    initialLanguageCode: string,
     *    content_is_user: bool,
     *    available_enabled_translations: iterable<Language>,
     *    available_translations: iterable<Language>,
     *    translation_language_code: string,
     *    resolvedLocation: Location
     *  }> $pagerSearchContentToDataMapper
     */
    public function __construct(
        private readonly Configurator $viewConfigurator,
        private readonly ParametersInjector $viewParametersInjector,
        private readonly SearchService $searchService,
        private readonly PagerSearchDataMapper $pagerSearchContentToDataMapper,
        private readonly QueryType $searchQueryType
    ) {}

    public function matches($argument): bool
    {
        return 'Ibexa\Bundle\Search\Controller\SearchController::searchAction' === $argument;
    }

    public function buildView(array $parameters): SearchView
    {
        $view = new SearchView();

        /** @var FormInterface $form */
        $form = $parameters['form'];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $searchLanguageCode = ($data->getSearchLanguage() instanceof Language)
                ? $data->getSearchLanguage()->languageCode
                : null;
            $languageFilter = $this->getSearchLanguageFilter($searchLanguageCode);

            $adapter = new ContentSearchHitAdapter(
                $this->searchQueryType->getQuery(['search_data' => $data]),
                $this->searchService,
                $languageFilter
            );

            $pagerfanta = new Pagerfanta($adapter);
            $pagerfanta->setMaxPerPage($data->getLimit());
            $pagerfanta->setCurrentPage(min($data->getPage(), $pagerfanta->getNbPages()));

            $results = $this->pagerSearchContentToDataMapper->map($pagerfanta);

            $view->addParameters([
                'results' => $results,
                'pager' => $pagerfanta,
                'aggregations' => $adapter->getAggregations(),
                'spellcheck' => $adapter->getSpellcheck(),
            ]);
        }

        $view->addParameters([
            'form' => $form->createView(),
        ]);

        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }

    private function getSearchLanguageFilter(?string $languageCode): array
    {
        return [
            'languages' => !empty($languageCode) ? [$languageCode] : [],
            'useAlwaysAvailable' => true,
        ];
    }
}
