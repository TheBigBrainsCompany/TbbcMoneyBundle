<?php
namespace Tbbc\MoneyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

/**
 * Class RatioFetchCommand
 * @package Tbbc\MoneyBundle\Command
 */
class RatioFetchCommand extends ContainerAwareCommand
{
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('tbbc:money:ratio-fetch')
            ->setHelp("The <info>tbbc:money:ratio-fetch</info> fetch all needed ratio from a external ratio provider")
            ->setDescription('fetch all needed ratio from a external ratio provider')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var PairManagerInterface $pairManager */
        $pairManager = $this->getContainer()->get("tbbc_money.pair_manager");
        try {
            $pairManager->saveRatioListFromRatioProvider();
            $output->writeln("ratio fetched from provider\n".print_r($pairManager->getRatioList(), true));
        } catch (MoneyException $e) {
            $output->writeln("ERROR during fetch ratio : ".$e->getMessage());
        }
    }
}
