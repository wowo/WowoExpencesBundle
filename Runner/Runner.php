<?php
namespace Wowo\ExpencesBundle\Runner;
use Wowo\ExpencesBundle\Factories\BankSummaryReader;
use Wowo\ExpencesBundle\Importer\IImporter;
use Wowo\ExpencesBundle\Importer\Mongo;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Runner object, which is kind of controlloer in fact
 * 
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class Runner
{
  protected $_directory;
  protected $_bank;
  protected $_type;
  protected $_importer;

  /**
   * __construct 
   * 
   * @access public
   * @return void
   */
  public function __construct($directory, $bank, $type, ContainerInterface $di, IImporter $importer = null)
  {
    $this->_directory = $directory;
    $this->_bank = $bank;
    $this->_type = $type;
    $this->_importer = ($importer == null) ? new Mongo($di->get('doctrine.odm.mongodb.document_manager')) : $importer;
  }

  /**
   * Runs expences process - retrieves data from disk and forms it into nice way
   * 
   * @access public
   * @return void
   */
  public function run()
  {
    $factory = new BankSummaryReader();
    $reader  = $factory->getBankSummaryReader($this->_bank, $this->_type);
    $result  = $reader->readFiles($this->_directory);
    $this->_importer->import($result);
    return $result;
  }
}
