<?php
namespace Wowo\ExpencesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wowo\ExpencesBundle\Runner\Runner;

/**
 * ImportOperationsCommand 
 * 
 * @uses Command
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class ImportOperationsCommand extends ContainerAwareCommand
{
  /**
   * configure 
   * 
   * @access protected
   * @return void
   */
  protected function configure()
  {
      parent::configure();
      $this
          ->setName("expences:import-operations")
          ->setDescription("Imports operations from given directory")
          ->addOption("dir",  "d", InputOption::VALUE_REQUIRED, "directory with files")
          ->addOption("bank", "b", InputOption::VALUE_OPTIONAL, "bank name", "mbank")
          ->addOption("type", "t", InputOption::VALUE_OPTIONAL, "type of reciept", "credit");
  }

  /**
   * execute 
   * 
   * @param InputInterface $input 
   * @param OutputInterface $output 
   * @access protected
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    try {
      $output->writeLn("<info>Expences calculator</info>");

      $dir = $input->getOption("dir");
      if (!$dir) {
        throw new \InvalidArgumentException("Please provide dir!");
      }

      $runner = new Runner($dir, $input->getOption("bank"), $input->getOption("type"), $this->application->getKernel()->getContainer());
      $operations = $runner->run();
      $output->writeLn($operations);
    } catch (\Exception $e) {
      throw $e;
      $output->writeLn(sprintf("Fatal error occured <error>(%s): %s</error>", get_class($e), $e->getMessage()));
    }
  }
}
