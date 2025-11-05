<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tbbc\MoneyBundle\Form\DataTransformer\MoneyToArrayTransformer;

/**
 * Formtype for the Money object.
 */
class MoneyType extends AbstractType
{
    public function __construct(protected int $decimals)
    {
    }

    /**
     * @psalm-suppress MixedArgument
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tbbc_amount', TextType::class, $options['amount_options'])
            ->add('tbbc_currency', $options['currency_type'], $options['currency_options'])
            ->addModelTransformer(
                new MoneyToArrayTransformer($this->decimals)
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
                'currency_type' => CurrencyType::class,
                'amount_options' => [],
                'currency_options' => [],
            ])
            ->setAllowedTypes(
                'currency_type',
                [
                    'string',
                    CurrencyType::class,
                ]
            )
            ->setAllowedTypes(
                'amount_options',
                'array'
            )
            ->setAllowedTypes(
                'currency_options',
                'array'
            )
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'tbbc_money';
    }
}
