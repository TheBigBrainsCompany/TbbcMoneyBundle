<?php

namespace Tbbc\MoneyBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

/**
 * Class RatioListCommand
 * @package Tbbc\MoneyBundle\Command
 */
class RatioListCommand extends Command
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
            ->setName('tbbc:money:ratio-list')
            ->setHelp('The <info>tbbc:money:ratio-list</info> display list of registered ratio')
            ->setDescription('display list of registered ratio');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ratioList = $this->pairManager->getRatioList();
        $output->writeln('Ratio list');
        foreach ($ratioList as $currencyCode => $ratio) {
            $output->writeln($currencyCode.';'.$ratio);
        }
    }
}
