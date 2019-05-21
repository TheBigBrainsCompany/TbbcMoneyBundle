<?php

namespace Tbbc\MoneyBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

/**
 * Class RatioSaveCommand
 * @package Tbbc\MoneyBundle\Command
 */
class RatioSaveCommand extends Command
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
            ->setName('tbbc:money:ratio-save')
            ->setHelp('The <info>tbbc:money:ratio-save</info> save a currency ratio')
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
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currencyCode = $input->getArgument('currencyCode');
        $ratio = (float) $input->getArgument('ratio');

        try {
            $this->pairManager->saveRatio($currencyCode, $ratio);
            $output->writeln('ratio saved');
        } catch (MoneyException $e) {
            $output->writeln('ERROR : ratio no saved du to error : '.$e->getMessage());
        }
    }
}
