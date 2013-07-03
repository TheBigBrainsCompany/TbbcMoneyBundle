<?php

namespace Tbbc\MoneyBundle\Form\Type;

use Tbbc\MoneyBundle\Form\DataTransformer\CurrencyToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
        $builder->add('tbbc_name', new ChoiceType(), array(
            "choices" => $choiceList,
            "preferred_choices" => array($options["reference_currency"])
        ));
        $builder->addModelTransformer(new CurrencyToArrayTransformer());
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('reference_currency', 'currency_choices'));
        $resolver->setDefaults(array(
            'reference_currency' => $this->referenceCurrencyCode,
            'currency_choices' => $this->currencyCodeList
        ));
        $resolver->setAllowedTypes(array(
            'reference_currency' => array('string'),
            'currency_choices' => array('array')
        ));
        $resolver->setAllowedValues(array(
            'reference_currency' => $this->currencyCodeList
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
