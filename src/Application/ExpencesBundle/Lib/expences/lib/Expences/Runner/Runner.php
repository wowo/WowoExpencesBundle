<?php
namespace Expences\Runner;
use Expences\Factories\BankSummaryReader;

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

  /**
   * __construct 
   * 
   * @access public
   * @return void
   */
  public function __construct($directory, $bank, $type)
  {
    $this->_directory = $directory;
    $this->_bank = $bank;
    $this->_type = $type;
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
    return $result;
  }
}
