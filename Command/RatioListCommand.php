<?php
namespace Tbbc\MoneyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

class RatioListCommand
    extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tbbc:money:ratio-list')
            ->setHelp("The <info>tbbc:money:ratio-list</info> display list of registered ratio")
            ->setDescription('display list of registered ratio')
        ;


    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var PairManagerInterface $pairManager */
        $pairManager = $this->getContainer()->get("tbbc_money.pair_manager");
        $ratioList = $pairManager->getRatioList();
        $output->writeln("Ratio list");
        foreach ($ratioList as $currencyCode => $ratio) {
            $output->writeln("$currencyCode;$ratio");
        }
    }



}