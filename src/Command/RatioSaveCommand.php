<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

class RatioSaveCommand extends Command
{
    public function __construct(private readonly PairManagerInterface $pairManager)
    {
        parent::__construct();
    }

    protected function configure(): void
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $currencyCode = (string) $input->getArgument('currencyCode');
        $ratio = (float) $input->getArgument('ratio');

        try {
            $this->pairManager->saveRatio($currencyCode, $ratio);
            $output->writeln('ratio saved');

            return Command::SUCCESS;
        } catch (MoneyException $e) {
            $output->writeln('ERROR : ratio no saved du to error : '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
