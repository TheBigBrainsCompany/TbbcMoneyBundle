<?php

namespace Tbbc\MoneyBundle\Tests\TestUtil;

use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Base class for testing the CLI tools.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
abstract class CommandTestCase extends WebTestCase
{
    /**
     * Runs a command and returns it output
     * @param KernelBrowser $client
     * @param $command
     * @return string
     * @throws Exception
     */
    public function runCommand(KernelBrowser $client, $command): string
    {
        $application = new Application($client->getKernel());
        $application->setAutoExit(false);

        $fp = tmpfile();
        $input = new StringInput($command);
        $output = new StreamOutput($fp);
        $application->run($input, $output);

        fseek($fp, 0);
        $output = "";
        while (!feof($fp)) {
            $output .= fread($fp, 4096)."\n";
        }
        fclose($fp);

        return $output;
    }
}