<?php

namespace Tbbc\MoneyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tbbc\MoneyBundle\Form\DataTransformer\MoneyToArrayTransformer;

/**
 * Form type for the Money object.
 */
class MoneyType
    extends AbstractType
{
    /** @var  int */
    protected $decimals;

    public function __construct(
        $decimals
    )
    {
        $this->decimals = (int)$decimals;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tbbc_amount', new TextType())
            ->add('tbbc_currency', $options['currency_type'])
            ->addModelTransformer(
                new MoneyToArrayTransformer($this->decimals)
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'currency_type' => 'tbbc_currency',
            ))
            ->setAllowedTypes(array(
                'currency_type' => array(
                    'string',
                    'Tbbc\MoneyBundle\Form\Type\CurrencyType',
                ),
            ))
        ;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'tbbc_money';
    }
}
