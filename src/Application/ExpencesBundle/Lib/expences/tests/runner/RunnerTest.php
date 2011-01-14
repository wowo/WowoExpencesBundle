<?php

require_once (__DIR__ . "/../BaseTestCase.php");

/**
 * RunnerTest 
 * 
 * @uses BaseTestCase
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class RunnerTest extends BaseTestCase
{
  /**
   * testRunnerForMbankCredit 
   * 
   * @access public
   * @return void
   */
  public function testRunnerForMbankCredit()
  {
    $config = new \expences\configuration\Runner($this->_fixturesDir . "/mbank_credit", "credit", "mbank");
    $runner = new \expences\runner\Runner($config);
    $operations = $runner->run();
    $this->operationAgainstFixturesTest($operations);
  }

  /**
   * operationAgainstFixturesTest 
   * 
   * @param mixed $operations 
   * @access public
   * @return void
   */
  public function operationAgainstFixturesTest($operations)
  {
    $this->assertType("array", $operations);
    $this->assertEquals(2, count($operations));
    $this->assertContainsOnly('expences\entities\Operation', $operations, false);
    $this->assertEquals(-50, $operations[0]->pricePln);
    $this->assertEquals("2007-04-10", $operations[1]->dateOperation);
  }
}
