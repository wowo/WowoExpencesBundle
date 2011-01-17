<?php
namespace Application\ExpencesBundle\Document;

/**
 * Bank operation 
 * 
 * @mongodb:Document(collection="operations")
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class Operation
{
  /**
   * @mongodb:Id
   */
  public $id;
  /**
   * @mongodb:Date
   */
  public $dateOperation;
  /**
   * @mongodb:Date
   */
  public $datePosting;
  /**
   * @mongodb:String
   */
  public $type;
  /**
   * @mongodb:String
   */
  public $description;
  /**
   * @mongodb:Float
   */
  public $priceOriginalCurrency;
  /**
   * @mongodb:Float
   */
  public $pricePln;

  /**
   * Converts object to string
   * 
   * @access public
   * @return string
   */
  public function __toString()
  {
    return sprintf("%s  %5.2f\t%-55s  %-40s", $this->dateOperation, $this->pricePln, $this->type, $this->description);
  }

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
