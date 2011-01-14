<?php

require_once (__DIR__ . "/../BaseTestCase.php");
/**
 * BankSummaryReaderTest 
 * 
 * @uses PHPUnit
 * @uses _Framework_TestCase
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class BankSummaryReaderTest extends BaseTestCase
{
  /**
   * testGetBankSummaryReaderProperData 
   * 
   * @access public
   * @return void
   */
  public function testGetBankSummaryReaderProperData()
  {
    $factory = new \expences\factories\BankSummaryReader();
    $this->assertInstanceOf('\expences\reader\mbank\CurrentAccount', $factory->getBankSummaryReader("mbank", "currentAccount"));
    $this->assertInstanceOf('\expences\reader\mbank\Credit', $factory->getBankSummaryReader("mbank", "credit"));
  }

  /**
   * testGetBankSummaryReaderFakeData 
   *
   * @dataProvider providerGetBankSummaryFakeData
   * @expectedException InvalidArgumentException
   * 
   * @param mixed $bank 
   * @param mixed $type 
   * @access public
   * @return void
   */
  public function testGetBankSummaryReaderFakeData($bank, $type)
  {
    $factory = new \expences\factories\BankSummaryReader();
    $factory->getBankSummaryReader($bank, $type);
  }

  /**
   * Provides fake data for testGetBankSummaryReaderFakeData
   * 
   * @codeCoverageIgnore
   *
   * @access public
   * @return void
   */
  public function providerGetBankSummaryFakeData()
  {
    return array(
      array("fakeBank", "credit"),
      array("fakeBank", "fakeType"),
      array("mbank", "fakeType"),
    );
  }
}
