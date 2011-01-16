<?php
namespace expences\reader\mbank;
use Expences\Reader\BankSummary;
use Expences\Reader\IBankSummary;

/**
 * Represents mBank current account monthly summary
 * 
 * @uses BankSummary
 * @uses expences
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class CurrentAccount extends BankSummary implements IBankSummary
{
  /**
   * readFiles 
   * 
   * @param mixed $directory 
   * @access public
   * @return void
   */
  public function readFiles($directory)
  {
    throw \Exception("not implemented");
  }
}
