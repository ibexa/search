<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Form\Type;

use Ibexa\Bundle\Search\Form\Data\SearchData;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType as CoreTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SearchType extends AbstractType
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        PermissionResolver $permissionResolver,
        ConfigResolverInterface $configResolver
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->configResolver = $configResolver;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('query', CoreTextType::class, ['required' => false])
            ->add('page', HiddenType::class, ['empty_data' => 1])
            ->add('limit', HiddenType::class, [
                'empty_data' => $this->configResolver->getParameter('search.pagination.limit'),
            ])
            ->add('content_types', ContentTypeChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('last_modified', DateIntervalType::class)
            ->add('created', DateIntervalType::class)
            ->add('search_in_users', SearchUsersType::class, [
                'property_path' => 'searchUsersData',
                'required' => false,
            ])
            ->add(
                'search_language',
                LanguageChoiceType::class,
                [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'placeholder' => /** @Desc("Choose an option") */ 'search.language.any',
                ]
            )
            ->add('subtree', HiddenType::class, [
                'required' => false,
            ])
        ;

        if ($this->permissionResolver->hasAccess('section', 'view') !== false) {
            $builder->add('section', SectionChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'placeholder' => /** @Desc("All") */ 'search.section.any',
            ]);
        }

        $builder->add(
            'sort',
            SortingDefinitionChoiceType::class,
            [
                'property_path' => 'sortingDefinition',
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'translation_domain' => 'ibexa_search',
        ]);
    }
}

class_alias(SearchType::class, 'Ibexa\Platform\Bundle\Search\Form\Type\SearchType');
