<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Form\Type;

use Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SortingDefintionChoiceType extends AbstractType
{
    /** @var iterable<\Ibexa\Contracts\Search\SortingDefinition\SortingDefintionProviderInterface> */
    private iterable $providers;

    /**
     * @param iterable<\Ibexa\Contracts\Search\SortingDefinition\SortingDefintionProviderInterface> $providers
     */
    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getSortingDefinitions(),
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

    /**
     * @return \Ibexa\Contracts\Search\SortingDefinition\SortingDefinitionInterface[]
     */
    private function getSortingDefinitions(): array
    {
        $definitions = [];
        foreach ($this->providers as $provider) {
            foreach ($provider->getSortingDefinitions() as $definition) {
                $definitions[] = $definition;
            }
        }

        usort(
            $definitions,
            static function (SortingDefinitionInterface $a, SortingDefinitionInterface $b): int {
                return $a->getPriority() <=> $b->getPriority();
            }
        );

        return $definitions;
    }
}
