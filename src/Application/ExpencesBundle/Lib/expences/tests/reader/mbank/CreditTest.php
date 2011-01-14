<?php

require_once (__DIR__ . "/../../BaseTestCase.php");

/**
 * Credit 
 * 
 * @uses BaseTestCase
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class CreditTest extends BaseTestCase
{
  protected $_mbankCreditFixturesDir;

  /**
   * setUp 
   * 
   * @access public
   * @return void
   */
  public function setUp()
  {
    parent::setUp();
    $this->_mbankCreditFixturesDir = $this->_fixturesDir . "/mbank_credit";
  }

  /**
   * testNonexistingDirectory 
   *
   * @expectedException InvalidArgumentException
   * 
   * @access public
   * @return void
   */
  public function testNonexistingDirectory()
  {
    $reader = new \expences\reader\mbank\Credit();
    $reader->readFiles("/nonexitant/directory/for/sure");
  }

  /**
   * testReaderWithFixtures 
   * 
   * @access public
   * @return void
   */
  public function testReaderWithFixtures()
  {
    $reader = new \expences\reader\mbank\Credit();
    $operations = $reader->readFiles($this->_mbankCreditFixturesDir);

    $runnerTest = new RunnerTest();
    $runnerTest->operationAgainstFixturesTest($operations);

  }
}
