<?php
namespace Tbbc\MoneyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

class SaveRatioCommand
    extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tbbc:money:save-ratio')
            ->setHelp("The <info>tbbc:money:save-ratio</info> save a currency ratio")
            ->setDescription('save a currency ratio')
            ->addArgument(
                'currencyCode',
                InputArgument::REQUIRED,
                'Currency (Ex: EUR|USD|...) ?'
            )
            ->addArgument(
                'ratio',
                InputArgument::REQUIRED,
                'Ratio to the reference currency (ex: 1.2563) ?'
            )
        ;


    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currencyCode = $input->getArgument('currencyCode');
        $ratio = floatval($input->getArgument('ratio'));

        /** @var PairManagerInterface $pairManager */
        $pairManager = $this->getContainer()->get("tbbc_money.pair_manager");
        try {
            $pairManager->saveRatio($currencyCode, $ratio);
            $output->writeln("ratio saved");
        } catch (MoneyException $e) {
            $output->writeln("ERROR : ratio no saved du to error : ".$e->getMessage());
        }
    }



}