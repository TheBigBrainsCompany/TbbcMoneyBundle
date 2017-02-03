<?php

namespace Tbbc\MoneyBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Tbbc\MoneyBundle\Form\DataTransformer\SimpleMoneyToArrayTransformer;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

/**
 * Form type for the Money object.
 */
class SimpleMoneyType extends MoneyType
{
    /** @var  PairManagerInterface */
    protected $pairManager;

    /** @var  int */
    protected $decimals;

    /**
     * SimpleMoneyType constructor.
     *
     * @param PairManagerInterface $pairManager
     * @param int                  $decimals
     */
    public function __construct(PairManagerInterface $pairManager, $decimals)
    {
        $this->pairManager = $pairManager;
        $this->decimals = (int) $decimals;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tbbc_amount', 'Symfony\Component\Form\Extension\Core\Type\TextType', $options['tbbc_amount_options'])
            ->addModelTransformer(
                new SimpleMoneyToArrayTransformer($this->pairManager, $this->decimals)
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tbbc_simple_money';
    }
}
