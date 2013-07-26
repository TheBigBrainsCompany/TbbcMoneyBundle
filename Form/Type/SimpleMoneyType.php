<?php

namespace Tbbc\MoneyBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Tbbc\MoneyBundle\Form\DataTransformer\MoneyToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tbbc\MoneyBundle\Form\DataTransformer\SimpleMoneyToArrayTransformer;
use Tbbc\MoneyBundle\Form\Type\CurrencyType;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

/**
 * Form type for the Money object.
 */
class SimpleMoneyType
    extends MoneyType
{

    /** @var  PairManagerInterface */
    protected $pairManager;
    public function __construct(PairManagerInterface $pairManager)
    {
        $this->pairManager = $pairManager;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tbbc_amount', new TextType())
            ->addModelTransformer(
                new SimpleMoneyToArrayTransformer($this->pairManager)
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
