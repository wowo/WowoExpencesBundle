<?php
namespace expences\output;

/**
 * IOutput 
 * 
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
interface IOutput
{
  /**
   * show 
   * 
   * @param array $operations 
   * @access public
   * @return void
   */
  public function show(array $operations);
}
