<?php
namespace expences\entities;

/**
 * Bank operation 
 * 
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class Operation
{
  public $dateOperation;
  public $datePosting;
  public $type;
  public $description;
  public $priceOriginalCurrency;
  public $pricePln;

  /**
   * Cleans up object (formats it values)
   * 
   * @access public
   * @return void
   */
  public function cleanup()
  {
    $this->_removeNewLinesAndTrim();
    $this->_convertAmounts();
  }

  /**
   * Removes new lines in every attribute
   * 
   * @access public
   * @return void
   */
  protected function _removeNewLinesAndTrim()
  {
    array_walk($this, function(&$value) {
      $value = str_replace("\n", "", $value);
      $value = trim($value);
    });
  }

  /**
   * convertAmounts 
   * 
   * @access public
   * @return void
   */
  protected function _convertAmounts()
  {
    $from = array(" ", ",");
    $to   = array("", ".");
    $this->priceOriginalCurrency = (float)str_replace($from, $to, $this->priceOriginalCurrency);
    $this->pricePln = (float)str_replace($from, $to, $this->pricePln);
  }
}
