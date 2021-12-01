<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\View;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Core\MVC\Symfony\View\Builder\ViewBuilder;
use Ibexa\Core\MVC\Symfony\View\Configurator;
use Ibexa\Core\MVC\Symfony\View\ParametersInjector;
use Ibexa\Core\Pagination\Pagerfanta\ContentSearchHitAdapter;
use Ibexa\Core\QueryType\QueryType;
use Ibexa\Search\Mapper\PagerSearchContentToDataMapper;
use Pagerfanta\Pagerfanta;

class SearchViewBuilder implements ViewBuilder
{
    /** @var \Ibexa\Core\MVC\Symfony\View\Configurator */
    private $viewConfigurator;

    /** @var \Ibexa\Core\MVC\Symfony\View\ParametersInjector */
    private $viewParametersInjector;

    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    private $searchService;

    /** @var \Ibexa\Search\Mapper\PagerSearchContentToDataMapper */
    private $pagerSearchContentToDataMapper;

    /** @var \Ibexa\Core\QueryType\QueryType */
    private $searchQueryType;

    public function __construct(
        Configurator $viewConfigurator,
        ParametersInjector $viewParametersInjector,
        SearchService $searchService,
        PagerSearchContentToDataMapper $pagerSearchContentToDataMapper,
        QueryType $searchQueryType
    ) {
        $this->viewConfigurator = $viewConfigurator;
        $this->viewParametersInjector = $viewParametersInjector;
        $this->searchService = $searchService;
        $this->pagerSearchContentToDataMapper = $pagerSearchContentToDataMapper;
        $this->searchQueryType = $searchQueryType;
    }

    public function matches($argument): bool
    {
        return 'Ibexa\Bundle\Search\Controller\SearchController::searchAction' === $argument;
    }

    public function buildView(array $parameters): SearchView
    {
        $view = new SearchView();

        /** @var \Symfony\Component\Form\FormInterface $form */
        $form = $parameters['form'];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $queryString = $data->getQuery();
            $searchLanguageCode = ($data->getSearchLanguage() instanceof Language)
                ? $data->getSearchLanguage()->languageCode
                : null;
            $languageFilter = $this->getSearchLanguageFilter($searchLanguageCode, $queryString);

            $pagerfanta = new Pagerfanta(
                new ContentSearchHitAdapter(
                    $this->searchQueryType->getQuery(['search_data' => $data]),
                    $this->searchService,
                    $languageFilter
                )
            );
            $pagerfanta->setMaxPerPage($data->getLimit());
            $pagerfanta->setCurrentPage(min($data->getPage(), $pagerfanta->getNbPages()));

            $view->addParameters([
                'results' => $this->pagerSearchContentToDataMapper->map($pagerfanta),
                'pager' => $pagerfanta,
            ]);
        }

        $view->addParameters([
            'form' => $form->createView(),
        ]);

        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }

    private function getSearchLanguageFilter(?string $languageCode, ?string $queryString): array
    {
        $filter = [
            'languages' => !empty($languageCode) ? [$languageCode] : [],
            'useAlwaysAvailable' => true,
        ];

        if (!empty($queryString)) {
            $filter['excludeTranslationsFromAlwaysAvailable'] = false;
        }

        return $filter;
    }
}

class_alias(SearchViewBuilder::class, 'Ibexa\Platform\Search\View\SearchViewBuilder');
