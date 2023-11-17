<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Form\Type;

use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionInterface;
use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SortingDefinitionChoiceType extends AbstractType
{
    private SortingDefinitionRegistryInterface $sortingDefinitionRegistry;

    public function __construct(SortingDefinitionRegistryInterface $sortingDefinitionRegistry)
    {
        $this->sortingDefinitionRegistry = $sortingDefinitionRegistry;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->sortingDefinitionRegistry->getSortingDefinitions(),
            'choice_label' => static function (?SortingDefinitionInterface $definition): ?string {
                if ($definition === null) {
                    return null;
                }

                return $definition->getLabel();
            },
            'choice_value' => static function (?SortingDefinitionInterface $definition): ?string {
                if ($definition === null) {
                    return null;
                }

                return $definition->getIdentifier();
            },
            'translation_domain' => 'ibexa_search',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
