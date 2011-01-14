<?php
namespace expences\reader\mbank;

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
class CurrentAccount extends \expences\reader\BankSummary implements \expences\reader\IBankSummary
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
