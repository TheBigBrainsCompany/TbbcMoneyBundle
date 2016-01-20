<?php

namespace Tbbc\MoneyBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Tbbc\MoneyBundle\Form\DataTransformer\SimpleMoneyToArrayTransformer;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

/**
 * Form type for the Money object.
 */
class SimpleMoneyType
    extends MoneyType
{
    /** @var  PairManagerInterface */
    protected $pairManager;

    /** @var  int */
    protected $decimals;

    public function __construct(PairManagerInterface $pairManager, $decimals)
    {
        $this->pairManager = $pairManager;
        $this->decimals = (int)$decimals;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tbbc_amount', get_class(new TextType()))
            ->addModelTransformer(
                new SimpleMoneyToArrayTransformer($this->pairManager, $this->decimals)
            );
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'tbbc_simple_money';
    }
}
