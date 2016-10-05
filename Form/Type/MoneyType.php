<?php

namespace Tbbc\MoneyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tbbc\MoneyBundle\Form\DataTransformer\MoneyToArrayTransformer;

/**
 * Form type for the Money object.
 */
class MoneyType extends AbstractType
{
    /** @var  int */
    protected $decimals;

    /**
     * MoneyType constructor.
     *
     * @param int $decimals
     */
    public function __construct($decimals)
    {
        $this->decimals = (int) $decimals;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tbbc_amount', 'Symfony\Component\Form\Extension\Core\Type\TextType')
            ->add('tbbc_currency', $options['currency_type'])
            ->addModelTransformer(
                new MoneyToArrayTransformer($this->decimals)
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => null,
                'currency_type' => 'Tbbc\MoneyBundle\Form\Type\CurrencyType',
            ))
            ->setAllowedTypes(
                'currency_type',
                array(
                    'string',
                    'Tbbc\MoneyBundle\Form\Type\CurrencyType',
                )
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tbbc_money';
    }
}
