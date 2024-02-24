<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Completion\Suggestion;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

class RatioListCommand extends Command
{
    private string $format = 'txt';

    public function __construct(private readonly PairManagerInterface $pairManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('tbbc:money:ratio-list')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, sprintf('The output format ("%s")', implode('", "', $this->getAvailableFormatOptions())), 'txt')
            ->setHelp('The <info>tbbc:money:ratio-list</info> display list of registered ratio')
            ->setDescription('display list of registered ratio');
    }

    /**
     * @param array<string, float> $ratioList
     */
    protected function displayTxt(array $ratioList, OutputInterface $output, SymfonyStyle $io): int
    {
        $io->writeln('Ratio list');

        foreach ($ratioList as $currencyCode => $ratio) {
            $io->writeln($currencyCode.';'.(string) $ratio);
        }

        return Command::SUCCESS;
    }

    /**
     * @param array<string, float> $ratioList
     */
    protected function displayTable(array $ratioList, OutputInterface $output, SymfonyStyle $io): int
    {
        $table = new Table($io);
        $table->setHeaderTitle('Ratio list');
        $table->setHeaders(['Currency', 'Ratio']);

        foreach ($ratioList as $currencyCode => $ratio) {
            $table->addRow([$currencyCode, $ratio]);
        }

        $table->render();

        return Command::SUCCESS;
    }

    /**
     * @param array<string, float> $ratioList
     * @throws \JsonException
     */
    protected function displayJson(array $ratioList, OutputInterface $output): int
    {
        $output->writeln(json_encode($ratioList, JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES));

        return Command::SUCCESS;
    }

    private function display(OutputInterface $output, SymfonyStyle $io): int
    {
        $ratioList = $this->pairManager->getRatioList();

        return match ($this->format) {
            'txt'   => $this->displayTxt($ratioList, $output, $io),
            'json'  => $this->displayJson($ratioList, $output),
            'table' => $this->displayTable($ratioList, $output, $io),
            default => throw new InvalidArgumentException(sprintf('Supported formats are "%s".', implode('", "', $this->getAvailableFormatOptions()))),
        };
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $format */
        $format = $input->getOption('format') ?? 'txt';
        $this->format = $format;

        return $this->display($output, $io);
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestOptionValuesFor('format')) {
            $suggestions->suggestValues($this->getAvailableFormatOptions());
        }
    }

    /** @return list<string|Suggestion> $values */
    private function getAvailableFormatOptions(): array
    {
        return ['txt', 'json', 'table'];
    }
}
