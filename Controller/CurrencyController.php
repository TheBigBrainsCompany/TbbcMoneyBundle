<?php

namespace Tbbc\MoneyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CurrencyController extends Controller
{
    public function listAction()
    {
        return $this->render('TbbcMoneyBundle:Currency:list.html.twig');
    }
}
