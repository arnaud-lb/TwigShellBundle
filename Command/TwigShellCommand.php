<?php

namespace Alb\TwigShellBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class TwigShellCommand extends ContainerAwareCommand
{
    private $container;
    private $hasReadline;
    private $output;
    private $prompt;
    private $history;
    private $twig;
    private $arrayLoader;

    public function configure()
    {
        $this->setName('twig:shell');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->getContainer();
        $this->twig = $this->container->get('twig');
        $this->hasReadline = function_exists('readline');
        $this->output = $output;
        $this->prompt = 'twig > ';
        $this->history = getenv('HOME').'/.history_'.$this->getApplication()->getName().'_twig';

        $this->prepareTwig();

        $this->output->writeln($this->getHeader());

        if ($this->hasReadline) {
            readline_read_history($this->history);
        }

        while (true) {

            $command = $this->readline();

            if (false === $command) {
                $this->output->writeln("\n");
                break;
            }

            if ($this->hasReadline) {
                readline_add_history($command);
                readline_write_history($this->history);
            }

            $code = sprintf("{{ %s }}", $command);

            try {
                $this->executeTwig($code);
                $output->writeln('');
            } catch(\Exception $e) {
                $this->getApplication()->renderException($e, $output);
            }
        }
    }

    /**
     * Returns the shell header.
     *
     * @return string The header string
     */
    protected function getHeader()
    {
        return <<<EOF

Welcome to the <info>Twig</info> shell.

Type any Twig expression as if it was enclosed by <Comment>{{ ... }}</Comment>.

To exit the shell, type <comment>^D</comment>.

EOF;
    }

    /**
     * Reads a single line from standard input.
     *
     * @return string The single line from standard input
     */
    private function readline()
    {
        if ($this->hasReadline) {
            $line = readline($this->prompt);
        } else {
            $this->output->write($this->prompt);
            $line = fgets(STDIN, 1024);
            $line = (!$line && strlen($line) == 0) ? false : rtrim($line);
        }

        return $line;
    }

    private function prepareTwig()
    {
        $this->arrayLoader = new \Twig_Loader_Array(array());
        $chainLoader = new \Twig_Loader_Chain(array(
            $this->arrayLoader,
            $this->twig->getLoader(),
        ));
        $this->twig->setLoader($chainLoader);
    }

    private function executeTwig($code)
    {
        $this->arrayLoader->setTemplate('command line code', $code);
        $this->twig->display('command line code');
    }
}
