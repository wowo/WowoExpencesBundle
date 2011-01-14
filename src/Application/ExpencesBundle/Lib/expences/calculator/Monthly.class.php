<?php
namespace expences\calculator;

/**
 * Calculates montly expences, and avarage
 * 
 * @uses Calculator
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class Monthly extends Calculator
{
  protected $_operations = array();

  /**
   * The constructor
   * 
   * @param array $operations 
   * @access public
   * @return void
   */
  public function __construct(array $operations)
  {
    $this->_operations = $operations;
  }

  public function getMonthlyExpences()
  {
  }
}
