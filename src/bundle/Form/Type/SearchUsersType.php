<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Form\Type;

use Ibexa\Bundle\Search\Form\Data\SearchUsersData;
use Ibexa\Bundle\Search\Form\DataTransformer\UsersTransformer;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SearchService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchUsersType extends AbstractType
{
    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    private $searchService;

    /** @var string */
    private $userContentTypeIdentifier;

    public function __construct(
        Repository $repository,
        SearchService $searchService,
        string $userContentTypeIdentifier
    ) {
        $this->repository = $repository;
        $this->searchService = $searchService;
        $this->userContentTypeIdentifier = $userContentTypeIdentifier;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(
            new UsersTransformer(
                $this->repository,
                $this->searchService,
                $this->userContentTypeIdentifier
            )
        );
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchUsersData::class,
        ]);
    }
}

class_alias(SearchUsersType::class, 'Ibexa\Platform\Bundle\Search\Form\Type\SearchUsersType');
