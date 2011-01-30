<?php
namespace Application\ExpencesBundle\Document;
use Application\ExpencesBundle\Repository\OperationRepository;

/**
 * Bank operation 
 * 
 * @mongodb:Document(collection="operations",repositoryClass="Application\ExpencesBundle\Repository\OperationRepository")
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
   * @mongodb:DateTime
   */
  public $createdAt;
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
   * @mongodb:Collection
   */
  public $tags;

  /**
   * Converts object to string
   * 
   * @access public
   * @return string
   */
  public function __toString()
  {
    $date = ($this->dateOperation instanceof \DateTime)
      ? $this->dateOperation->format("Y-m-d")
      : $this->dateOperation;
    return sprintf("%s  %5.2f %-40s", $date, $this->pricePln, strtolower($this->description));
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

  public function getPricePlnFormatted()
  {
    return number_format($this->pricePln, 2, ",", " ") . " zł";
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

  /**
   * getTags 
   * 
   * @access public
   * @return array
   */
  public function getTags()
  {
    return ($this->tags != null) ? $this->tags : array();
  }

  public function getPrice()
  {
    return $this->pricePln;
  }

  public function setPrice($value)
  {
    $this->pricePln = $value;
    $this->priceOriginalCurrency = $value;
  }

  public function getDate()
  {
    return $this->dateOperation;
  }

  public function setDate($value)
  {
    $this->dateOperation = $value;
    $this->datePosting = new \DateTime("now");
  }

  public function getTagsValues()
  {
    return implode(",", (array)$this->tags);
  }

  public function setTagsValues($value)
  {
    $this->tags = explode(",", $value);
  }
    
}
