<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Search\View;

use Ibexa\Bundle\Search\Form\Data\SearchData;
use Ibexa\Bundle\Search\Form\Type\SearchType;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\View\Event\FilterViewBuilderParametersEvent;
use Ibexa\Core\MVC\Symfony\View\ViewEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SearchViewFilter implements EventSubscriberInterface
{
    private ConfigResolverInterface $configResolver;

    private FormFactoryInterface $formFactory;

    private SectionService $sectionService;

    private ContentTypeService $contentTypeService;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        ConfigResolverInterface $configResolver,
        FormFactoryInterface $formFactory,
        SectionService $sectionService,
        ContentTypeService $contentTypeService,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->configResolver = $configResolver;
        $this->formFactory = $formFactory;
        $this->sectionService = $sectionService;
        $this->contentTypeService = $contentTypeService;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [ViewEvents::FILTER_BUILDER_PARAMETERS => 'handleSearchForm'];
    }

    /**
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function handleSearchForm(FilterViewBuilderParametersEvent $event): void
    {
        $controllerAction = $event->getParameters()->get('_controller');

        if (
            'Ibexa\Bundle\Search\Controller\SearchController::searchAction' !== $controllerAction
        ) {
            return;
        }

        $request = $event->getRequest();

        $search = $request->query->all('search');
        $limit = isset($search['limit']) ? (int)$search['limit'] : $this->configResolver->getParameter('search.pagination.limit');
        $page = isset($search['page']) ? (int)$search['page'] : 1;
        $query = $search['query'] ?? '';
        $section = null;
        $creator = null;
        $contentTypes = [];
        $lastModified = $search['last_modified'] ?? [];
        $created = $search['created'] ?? [];
        $subtree = $search['subtree'] ?? null;
        $searchLanguage = null;

        if (!empty($search['section'])) {
            $section = $this->sectionService->loadSection($search['section']);
        }

        if (!empty($search['content_types']) && \is_array($search['content_types'])) {
            foreach ($search['content_types'] as $identifier) {
                $contentTypes[] = $this->contentTypeService->loadContentTypeByIdentifier($identifier);
            }
        }

        $form = $this->formFactory->create(
            SearchType::class,
            new SearchData(
                $limit,
                $page,
                $query,
                $section,
                $contentTypes,
                $lastModified,
                $created,
                $creator,
                $subtree,
                $searchLanguage
            ),
            [
                'method' => Request::METHOD_GET,
                'csrf_protection' => false,
                'action' => $this->urlGenerator->generate('ibexa.search'),
            ]
        );

        $event->getParameters()->add([
            'form' => $form->handleRequest($request),
        ]);
    }
}
