<?php

namespace Tbbc\MoneyBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Tbbc\MoneyBundle\Form\DataTransformer\MoneyToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Tbbc\MoneyBundle\Form\Type\CurrencyType;

/**
 * Form type for the Money object.
 */
class MoneyType
    extends AbstractType
{
    /** @var  CurrencyType */
    protected $currencyType;

    /** @var  int */
    protected $decimals;

    public function __construct(
        CurrencyType $currencyType,
        $decimals
    )
    {
        $this->currencyType = $currencyType;
        $this->decimals = (int)$decimals;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tbbc_amount', new TextType())
            ->add('tbbc_currency', $this->currencyType->getName())
            ->addModelTransformer(
                new MoneyToArrayTransformer($this->decimals)
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
