<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Form\Type;

use Ibexa\Bundle\Search\Form\DataTransformer\DateIntervalTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class DateIntervalType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start_date', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime',
            ])
            ->add('end_date', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime',
            ])
            ->addModelTransformer(new DateIntervalTransformer());
    }
}

class_alias(DateIntervalType::class, 'Ibexa\Platform\Bundle\Search\Form\Type\DateIntervalType');
