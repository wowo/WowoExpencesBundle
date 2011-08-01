<?php
namespace Wowo\ExpencesBundle\Importer;

/**
 * IImporter 
 * 
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
interface IImporter
{
  public function import(array $operations);
}
