<?php
namespace Application\ExpencesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use expences\output\Stdout;
use expences\output\Logger;
use expences\runner\Runner;
use expences\configuration\Runner as RunnerConfig;
use expences\exceptions\PhpConfiguration;

class ImportOperationsCommand extends Command
{
  protected function configure()
  {
      parent::configure();
      $this
          ->setName('expences:import-operations')
          ->addOption('dir',  'd', InputOption::VALUE_REQUIRED, 'directory with files', '')
          ->addOption('bank', 'b', InputOption::VALUE_REQUIRED, 'bank name', '')
          ->addOption('type', 't', InputOption::VALUE_OPTIONAL, 'type of reciept', 'credit')

      ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    try {
      $output = new Stdout();
      $logger = new Logger("php://stderr");
      $logger->log("Expences calculator");

      $config = new RunnerConfig($input->getOption("dir"), $input->getOption("bank"), $input->getOption("type"));
      $runner = new Runner($config);
      $runner->checkConfiguration();

      $operations = $runner->run();
      $logger->log(sprintf("Retreived %d operations", count($operations)));
      $output->show($operations);

      $logger->log("Finished");
    } catch (PhpConfiguration $e) {
      $logger->log($e->getMessage(), LOG_ERR);
    } catch (\Exception $e) {
      $logger->log(sprintf("Fatal error occured (%s): %s", get_class($e), $e->getMessage()), LOG_CRIT);
    }
  }
}
