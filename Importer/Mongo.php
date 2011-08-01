<?php
namespace Wowo\ExpencesBundle\Importer;
use Wowo\ExpencesBundle\Importer\IImporter;
use Wowo\ExpencesBundle\Document\Operation;

/**
 * Imports operations to MongoDB
 * 
 * @uses IImporter
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class Mongo implements IImporter
{
  protected $_dm;

  public function __construct(\Doctrine\ODM\MongoDB\DocumentManager $dm)
  {
    $this->_dm = $dm;
  }
  /**
   * import 
   * 
   * @param array $operations 
   * @access public
   * @return void
   */
  public function import(array $operations)
  {
    foreach ($operations as $operation) {
      if (!($operation instanceof Operation)) {
        throw new \InvalidArgumentException("Given operation object isn't an Operation instance");
      }
      $this->_dm->persist($operation);
      $this->_dm->flush();
    }
  }

  public function importFromUpload(array $operations, $user, $bank, $summaryType)
  {
    foreach ($operations as $operation) {
      if (!($operation instanceof Operation)) {
        throw new \InvalidArgumentException("Given operation object isn't an Operation instance");
      }
      $operation->createdAt = new \DateTime("now");
      $operation->bank = $bank;
      $operation->summaryType = $summaryType;
      $operation->user = $user;
      $this->_dm->persist($operation);
    }
    $this->_dm->flush();
  }
}
