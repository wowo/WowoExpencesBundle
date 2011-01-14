<?php
namespace expences\configuration;

/**
 * Runner 
 * 
 * @uses IConfiguration
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class Runner implements IConfiguration
{
  /**
   * directory storing summaries
   * 
   * @var mixed
   * @access public
   */
  public $dataDirectory;
  /**
   * type of the summary (current/credit)
   * 
   * @var mixed
   * @access public
   */
  public $type;
  /**
   * bank name 
   * 
   * @var mixed
   * @access public
   */
  public $bank;

  /**
   * Constructor
   * 
   * @param mixed $dataDirectory 
   * @param mixed $type 
   * @param mixed $bank 
   * @access public
   * @return void
   */
  public function __construct($dataDirectory, $type, $bank)
  {
    $this->dataDirectory = $dataDirectory;
    $this->type = $type;
    $this->bank = $bank;
  }
}
