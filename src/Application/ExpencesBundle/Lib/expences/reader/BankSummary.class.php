<?php
namespace expences\reader;

/**
 * BankSummary 
 * 
 * @abstract
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
abstract class BankSummary
{
  protected function _getXmls($directory)
  {
    $xmls = array();
    if (!file_exists($directory)) {
      throw new \InvalidArgumentException(sprintf("Directory %s doesn't exists!", $directory));
    }
    foreach (new \DirectoryIterator($directory) as $file) {
      if ($file->isFile()) {
        $path = sprintf("%s/%s", $directory, (string)$file);
        $xmlString   = file_get_contents($path);
        $repaired    = $this->_cleanXml($xmlString);
        $xmls[$path] = $this->_getXml($repaired);
      }
    }
    return $xmls;
  }

  /**
   * Tidies up xml file and returns repaired content
   * 
   * @param mixed $path 
   * @access protected
   * @return string
   */
  protected function _cleanXml($xmlString)
  {
    $xmlString = iconv("windows-1250", "utf8", $xmlString);
    $xmlString = $this->_tidyXml($xmlString);
    $xmlString = str_replace("&nbsp;", "", $xmlString);
    return $xmlString;
  }

  /**
   * Tidy XML
   * 
   * @param mixed $xmlString 
   * @access protected
   * @return void
   */
  protected function _tidyXml($xmlString)
  {
    $tidy = new \tidy(); 
    $config = array(
      "clean" => true,
      "show-body-only" => false,
      "add-xml-decl" => true,
      "output-xhtml" => true,
      "quote-nbsp" => true,
    ); 
    $tidy->parseString($xmlString, $config, "utf8"); 
    $tidy->cleanRepair(); 
    return (string)$tidy;
  }

  /**
   * Builds simplexml object based on string
   * 
   * @param mixed $xmlString 
   * @access protected
   * @return SimpleXmlElement
   */
  protected function _getXml($xmlString)
  {
    $sxe = simplexml_load_string($xmlString);
    return $sxe;
  }
}
