<?php
namespace Wowo\ExpencesBundle\Reader;

/**
 * IBankSummary 
 * 
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
interface IBankSummary
{
  public function readFiles($directory);
  public function getOperations($xmlString);
}
