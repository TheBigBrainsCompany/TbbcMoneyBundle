<?php

namespace Tbbc\MoneyBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Tbbc\MoneyBundle\Form\DataTransformer\MoneyToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tbbc\MoneyBundle\Form\Type\CurrencyType;

/**
 * Form type for the Money object.
 */
class MoneyType
    extends AbstractType
{
    /** @var  CurrencyType */
    protected $currencyType;

    public function __construct(
        CurrencyType $currencyType
    )
    {
        $this->currencyType = $currencyType;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tbbc_amount', new NumberType())
            ->add('tbbc_currency', $this->currencyType)
            ->addModelTransformer(
                new MoneyToArrayTransformer()
            );
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'tbbc_money';
    }
}
