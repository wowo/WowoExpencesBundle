<?php
namespace expences\runner;

/**
 * Runner object, which is kind of controlloer in fact
 * 
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class Runner
{
  /**
   * _config 
   * 
   * @var IConfiguration
   * @access protected
   */
  protected $_config = null;

  /**
   * __construct 
   * 
   * @access public
   * @return void
   */
  public function __construct(\expences\configuration\IConfiguration $config)
  {
    $this->_config = $config;
  }

  /**
   * Registers autoload
   * 
   * @access protected
   * @return void
   */
  public static function autoloadRegister($verbose = false)
  {
    spl_autoload_register(function($className) use ($verbose) {
      $level = count(explode("\\", __NAMESPACE__));
      $classPath = str_replace("\\", "/", $className);
      $path = sprintf("%s/%s%s.class.php", __DIR__, str_repeat("../", $level), $classPath);
      if ($verbose) {
        printf("\t%s -> %s\n", $className, $path);
      }
      if (file_exists($path)) {
        require_once($path);
      }
      return true;
    });
  }

  /**
   * Runs expences process - retrieves data from disk and forms it into nice way
   * 
   * @access public
   * @return void
   */
  public function run()
  {
    $factory = new \expences\factories\BankSummaryReader();
    $reader  = $factory->getBankSummaryReader($this->_config->bank, $this->_config->type);
    $result  = $reader->readFiles($this->_config->dataDirectory);
    return $result;
  }

  /**
   * checkConfiguration 
   * 
   * @codeCoverageIgnore
   *
   * @access public
   * @return void
   */
  public function checkConfiguration()
  {
    if (!class_exists('\tidy')) {
      throw new \expences\exceptions\PhpConfiguration("Tidy module not loaded!");
    }
    if (!class_exists('\Mongo')) {
      throw new \expences\exceptions\PhpConfiguration("Mongo module not loaded!");
    }
  }
}
