<?php

namespace Tbbc\MoneyBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tbbc\MoneyBundle\Form\DataTransformer\SimpleMoneyToArrayTransformer;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

/**
 * Form type for the Money object.
 */
class SimpleMoneyType extends MoneyType
{
    /** @var  int */
    protected $decimals;

    /** @var  array of string (currency code like "USD", "EUR") */
    protected $currencyCodeList;

    /** @var  string (currency code like "USD", "EUR") */
    protected $referenceCurrencyCode;

    /**
     * @param int    $decimals
     * @param array  $currencyCodeList
     * @param string $referenceCurrencyCode
     */
    public function __construct(
        $decimals,
        $currencyCodeList,
        $referenceCurrencyCode
    ) {
        $this->decimals = (int) $decimals;
        $this->currencyCodeList = $currencyCodeList;
        $this->referenceCurrencyCode = $referenceCurrencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tbbc_amount', 'Symfony\Component\Form\Extension\Core\Type\TextType')
        ;

        $transformer = new SimpleMoneyToArrayTransformer($this->decimals);
        $transformer->setCurrency($options['currency']);

        $builder
            ->addModelTransformer($transformer)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tbbc_simple_money';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'currency' => $this->referenceCurrencyCode,
        ));
        $resolver->setAllowedTypes('currency', 'string');
        $resolver->setAllowedValues('currency', $this->currencyCodeList);
    }

    /**
     * BC for SF < 2.7
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }
}
