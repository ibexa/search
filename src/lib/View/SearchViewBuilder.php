<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\View;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Search\Mapper\PagerSearchDataMapper;
use Ibexa\Core\MVC\Symfony\View\Builder\ViewBuilder;
use Ibexa\Core\MVC\Symfony\View\Configurator;
use Ibexa\Core\MVC\Symfony\View\ParametersInjector;
use Ibexa\Core\Pagination\Pagerfanta\ContentSearchHitAdapter;
use Ibexa\Core\QueryType\QueryType;
use Pagerfanta\Pagerfanta;

class SearchViewBuilder implements ViewBuilder
{
    /**
     * @param \Ibexa\Contracts\Search\Mapper\PagerSearchDataMapper<array{
     *    content: \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *    contentTypeId: int,
     *    contentId: int,
     *    name: string,
     *    language: string,
     *    contributor: \Ibexa\Contracts\Core\Repository\Values\User\User|null,
     *    version: int,
     *    content_type: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType,
     *    modified: \DateTimeInterface,
     *    initialLanguageCode: string,
     *    content_is_user: bool,
     *    available_enabled_translations: iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Language>,
     *    available_translations: iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Language>,
     *    translation_language_code: string,
     *    resolvedLocation: \Ibexa\Contracts\Core\Repository\Values\Content\Location
     *  }> $pagerSearchContentToDataMapper
     */
    public function __construct(
        private readonly Configurator $viewConfigurator,
        private readonly ParametersInjector $viewParametersInjector,
        private readonly SearchService $searchService,
        private readonly PagerSearchDataMapper $pagerSearchContentToDataMapper,
        private readonly QueryType $searchQueryType
    ) {
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
