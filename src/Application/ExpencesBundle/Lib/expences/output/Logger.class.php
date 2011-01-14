<?php
namespace expences\output;

/**
 * Logger 
 * 
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class Logger
{
  /**
   * Output pattern (decorator)
   * 
   * @var string
   * @access protected
   */
  protected $_pattern;
  /**
   * Output stream
   * 
   * @var mixed
   * @access protected
   */
  protected $_stream;
  /**
   * Date format 
   * 
   * @var mixed
   * @access protected
   */
  protected $_dateFormat;

  /**
   * PHP compatibile log levels
   * 
   * @var array
   * @access protected
   */
  protected $_logLevels = array(
    LOG_EMERG => "emerg",
    LOG_ALERT => "alert",
    LOG_CRIT => "crit",
    LOG_ERR => "err",
    LOG_WARNING => "warning",
    LOG_NOTICE => "notice",
    LOG_INFO => "info",
    LOG_DEBUG => "debug",
  );

  /**
   * The constructor 
   * 
   * @param   public function__construct( $  public functionpattern 
   * @param string $stream 
   * @access public
   * @return void
   */
  public function __construct($stream = "php://stdout", $dateFormat = "H:i:s", $pattern = '>> [%2$7s] (%3$s) %1$s')
  {
    $this->_pattern = $pattern;
    $this->_dateFormat = $dateFormat;
    $this->_stream = $stream;
  }

  /**
   * Logs to the stream
   * 
   * @param mixed $message 
   * @access public
   * @return void
   */
  public function log($message, $level = LOG_INFO)
  {
    $msg = sprintf($this->_pattern, $message . PHP_EOL, $this->_logLevels[$level], date($this->_dateFormat));
    $stream = fopen($this->_stream, "a+");
    fwrite($stream, $msg);
  }
}
