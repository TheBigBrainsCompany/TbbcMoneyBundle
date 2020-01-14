<?php

namespace Tbbc\MoneyBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

/**
 * Class RatioFetchCommand
 * @package Tbbc\MoneyBundle\Command
 */
class RatioFetchCommand extends Command
{

    /**
     * @var PairManagerInterface
     */
    private $pairManager;

    /**
     * @param PairManagerInterface $pairManager
     */
    public function __construct(PairManagerInterface $pairManager)
    {
        parent::__construct();
        $this->pairManager = $pairManager;
    }

    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('tbbc:money:ratio-fetch')
            ->setHelp('The <info>tbbc:money:ratio-fetch</info> fetch all needed ratio from a external ratio provider')
            ->setDescription('fetch all needed ratio from a external ratio provider');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->pairManager->saveRatioListFromRatioProvider();
            $output->writeln('ratio fetched from provider'.PHP_EOL.print_r($this->pairManager->getRatioList(), true));
        } catch (MoneyException $e) {
            $output->writeln('ERROR during fetch ratio : '.$e->getMessage());
        }
    }
}
