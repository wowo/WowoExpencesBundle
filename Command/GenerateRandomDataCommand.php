<?php
namespace Wowo\ExpencesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wowo\ExpencesBundle\Document\Operation;

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
class GenerateRandomDataCommand extends ContainerAwareCommand
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
          ->setName("expences:generate-random-data")
          ->setDescription("Generates random data");
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
    $output->writeLn("<info>Generating data</info>");
    $dm = $this->getContainer()->get('doctrine.odm.mongodb.document_manager');


    $tags = array("paliwo", "zakupy", "rozrywka");
    $desc = array("Lorem ipsum dolor sit amet", "consectetur adipiscing elit", "libero sed convallis vulputate");
    mt_srand();
    for ($month = 1; $month <= 12; $month++) {
      for ($j = 0; $j < mt_rand(15, 30); $j++) {
        $operation = new Operation();
        $operation->dateOperation = new \DateTime("2010-" . $month . "-" . mt_rand(1, 28));
        $operation->datePosting = $operation->dateOperation;
        $operation->type = "Płatność kartą";
        $operation->description = $desc[array_rand($desc)];;
        $operation->priceOriginalCurrency = mt_rand(1.0, 100.00);
        $operation->pricePln = $operation->priceOriginalCurrency;
        $operation->tags = array($tags[array_rand($tags)]);
        $dm->persist($operation);
        $dm->flush();
      }
    }
  }
}

