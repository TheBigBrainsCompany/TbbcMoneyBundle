<?php

namespace Tbbc\MoneyBundle\Form\Type;

use Tbbc\MoneyBundle\Form\DataTransformer\CurrencyToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
        $choiceList = array();
        foreach ($options["currency_choices"] as $currencyCode) {
            $choiceList[$currencyCode] = $currencyCode;
        }
        $builder->add('tbbc_name', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            "choices" => $choiceList,
            "preferred_choices" => array($options["reference_currency"])
        ));
        $builder->addModelTransformer(new CurrencyToArrayTransformer());
    }

    /**
     * @inheritdoc
     */
     public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array('reference_currency', 'currency_choices'));
        $resolver->setDefaults(array(
            'reference_currency' => $this->referenceCurrencyCode,
            'currency_choices' => $this->currencyCodeList
        ));
        $resolver->setAllowedTypes('reference_currency', 'string');
        $resolver->setAllowedTypes('currency_choices', 'array');
        $resolver->setAllowedValues('reference_currency', $this->currencyCodeList);
    }

    // BC for SF < 2.7
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'tbbc_currency';
    }

}
