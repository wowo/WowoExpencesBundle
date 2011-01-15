<?php
namespace Expences\Factories;
use \Expences\Reader\Mbank\Credit;
use \Expences\Reader\Mbank\CurrentAccount;

/**
 * Bank Summary Reader factory
 * 
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class BankSummaryReader
{
  /**
   * get bank summary reader
   * 
   * @param mixed $bank 
   * @param mixed $type 
   * @access public
   * @return IBankSummary
   */
  public function getBankSummaryReader($bank, $type)
  {
    switch ($bank) {
      case "mbank":
          switch ($type) {
            case "credit":
              return new Credit();
            case "currentAccount":
              return new CurrentAccount();
            default:
              throw new \InvalidArgumentException(sprintf("No class for mbank '%s' account type", $type), 1);
          }
        break;
      default:
        throw new \InvalidArgumentException(sprintf("No class for '%s' bank", $bank), 2);
        break;
    }
  }
}
