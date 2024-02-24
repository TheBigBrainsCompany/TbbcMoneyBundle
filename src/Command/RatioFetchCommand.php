<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

class RatioFetchCommand extends Command
{
    public function __construct(private readonly PairManagerInterface $pairManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('tbbc:money:ratio-fetch')
            ->setHelp('The <info>tbbc:money:ratio-fetch</info> fetch all needed ratio from a external ratio provider')
            ->setDescription('fetch all needed ratio from a external ratio provider');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->pairManager->saveRatioListFromRatioProvider();
            $output->writeln('ratio fetched from provider'.PHP_EOL.print_r($this->pairManager->getRatioList(), true));

            return Command::SUCCESS;
        } catch (MoneyException $e) {
            $output->writeln('ERROR during fetch ratio : '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
