<?php

namespace Container3VVPK3j;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/*
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getForm_ChoiceListFactory_DefaultService extends Tbbc_MoneyBundle_Tests_AppKernelTestContainer
{
    /*
     * Gets the private 'form.choice_list_factory.default' shared service.
     *
     * @return \Symfony\Component\Form\ChoiceList\Factory\DefaultChoiceListFactory
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['form.choice_list_factory.default'] = new \Symfony\Component\Form\ChoiceList\Factory\DefaultChoiceListFactory();
    }
}
