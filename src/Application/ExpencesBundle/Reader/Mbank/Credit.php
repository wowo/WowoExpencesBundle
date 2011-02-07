<?php
namespace Application\ExpencesBundle\Reader\Mbank;
use Application\ExpencesBundle\Reader\BankSummary;
use Application\ExpencesBundle\Reader\IBankSummary;
use Application\ExpencesBundle\Document\Operation;

/**
 * Reads mbank credit card summaries
 * 
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class Credit extends BankSummary implements IBankSummary
{
  /**
   * read files
   * 
   * @param mixed $directory 
   * @access public
   * @return array
   */
  public function readFiles($directory)
  {
    $result = array();
    $xmls = $this->_getXmls($directory);
    foreach ($xmls as $path => $xml) {
      $result = array_merge($result, $this->_getOperationsFromXml($xml));
    }
    return $result;
  }

  public function getOperations($xmlString)
  {
    $xml = $this->_prepareXml($xmlString);
    return $this->_getOperationsFromXml($xml);
  }

  /**
   * _getOperationsFromXml 
   * 
   * @param SimpleXmlElement $xml 
   * @access protected
   * @return array
   */
  protected function _getOperationsFromXml(\SimpleXMLElement $xml)
  {
    $operations = array();
    $xml->registerXPathNamespace("ns", "http://www.w3.org/1999/xhtml");
    $result = $xml->xpath("//ns:tr/ns:td[position()=1 and text()>=1]/..");
    foreach ($result as $sxe) {
      $operation = new Operation();
      $dates = explode("\n", (string)$sxe->td[1]);
      $operation->dateOperation = $dates[0];
      $operation->datePosting = $dates[1];
      $operation->type = (string)$sxe->td[2];
      $operation->description = (string)$sxe->td[3];
      $operation->priceOriginalCurrency = (string)$sxe->td[5];
      $operation->pricePln = (string)$sxe->td[5];
      $operation->cleanup();
      $operations[] = $operation;
    }
    return $operations;
  }
}
