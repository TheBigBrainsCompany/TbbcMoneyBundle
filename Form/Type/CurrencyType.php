<?php

namespace Tbbc\MoneyBundle\Form\Type;

use Tbbc\MoneyBundle\Form\DataTransformer\CurrencyToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type for the Currency object.
 */
class CurrencyType
    extends AbstractType
{
    /** @var  array of string (currency code like "USD", "EUR") */
    protected $currencyCodeList;
    /** @var  string (currency code like "USD", "EUR") */
    protected $referenceCurrencyCode;

    public function __construct(
        $currencyCodeList,
        $referenceCurrencyCode
    )
    {
        $this->currencyCodeList = $currencyCodeList;
        $this->referenceCurrencyCode = $referenceCurrencyCode;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CurrencyToStringTransformer());
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('currency', 'currency_choices'));
        $resolver->setDefaults(array(
            'currency' => $this->referenceCurrencyCode,
            'currency_choices' => $this->currencyCodeList
        ));
        $resolver->setAllowedTypes(array(
            'currency' => array('string'),
            'choices' => array('array')
        ));
        $resolver->setAllowedValues(array(
            'currency' => $this->currencyCodeList
        ));
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'tbbc_currency';
    }

}
