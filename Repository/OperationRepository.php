<?php
namespace Wowo\ExpencesBundle\Repository;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Wowo\ExpencesBundle\Document\User;

/**
 * OperationRepository 
 * 
 * @uses DocumentRepository
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class OperationRepository extends DocumentRepository
{
  /**
   * Adds few tags for given by ids operations
   * 
   * @param array $ids 
   * @param array $tags 
   * @access public
   * @return void
   */
  public function addTagsForOperations(array $ids, array $tags)
  {
    if (count($tags) > 0 && count($ids) > 0) {
      $mongoIds = array();
      foreach ($ids as $id) {
        $mongoIds[] = new \MongoId($id);
      }
      $query = $this->createQueryBuilder();
      $query->field("id")->in($mongoIds);
      $operations = $query->getQuery()->execute();
      if (count($operations) == 0) {
        throw new \RuntimeException("Couldn't retrieve given operations");
      }
      foreach ($operations as $operation) {
        $newTags = is_array($operation->tags) ? array_merge($operation->tags, $tags) : $tags;
        $newTags = array_unique($newTags);
        $operation->tags = $newTags;
        $this->dm->persist($operation);
      }
      $this->dm->flush();
    }
  }
  /**
   * getOperationsForUser 
   * 
   * @param User $user 
   * @param mixed $tags 
   * @param mixed $search 
   * @access public
   * @return void
   */
  public function getOperationsForUser(User $user, $tag = null, $search = null)
  {
    $query = $this->createQueryBuilder();
    $query->field("user")->references($user);
    $query->sort("dateOperation", "desc");
    if ($search) {
      $query->field("description")->equals(new \MongoRegex(sprintf("/%s/i", $search)));
    }
    if ($tag) {
      $query->field("tags")->in(array($tag));
    }
    return $query->getQuery()->execute();
  }

  /**
   * _getOperationsSummary 
   * 
   * @param mixed $mapFunction 
   * @access protected
   * @return void
   */
  public function getMonthlyOperationsSummary()
  {
    $mapFunction = "
      function() { 
        var month = (this.dateOperation.getMonth() + 1);
        if (month < 10) {
          month = '0' + month;
        }
        var key = this.dateOperation.getFullYear() + '-' + month;
        emit (key, this.pricePln);
      }";
    return $this->_getOperationsSummary($mapFunction);
  }

  /**
   * getYearlyOperationsSummary 
   * 
   * @access public
   * @return void
   */
  public function getYearlyOperationsSummary()
  {
    $mapFunction = "
      function() { 
        emit (this.dateOperation.getFullYear(), this.pricePln);
      }";
    return $this->_getOperationsSummary($mapFunction);
  }

  /**
   * getTagsOperationsSummary 
   * 
   * @access public
   * @return void
   */
  public function getTagsOperationsSummary()
  {
    $rows = array();
    $query = $this->createQueryBuilder();

    $query->sort("dateOperation", "asc")
      ->map("
        function() { 
          if (this.tags && this.tags.length > 0) {
            for (var i = 0; i < this.tags.length; i++) {
              var month = (this.dateOperation.getMonth() + 1);
              if (month < 10) {
                month = '0' + month;
              }
              var key = this.dateOperation.getFullYear() + '-' + month;
              emit ({tag: this.tags[i], month: key}, {amount: this.pricePln});
            }
          }
        }"
      )
      ->reduce("
        function (key, values) {
          var sum = 0;
          for (var i = 0; i < values.length; i++) {
            sum += values[i].amount;
          }
          return {amount: sum};
        }"
      );

    $result = array();
    $tmp = $query->getQuery()->execute();
    foreach ($tmp as $value) {
      if (!isset($result[$value["_id"]["tag"]])) {
        $result[$value["_id"]["tag"]] = array();
      }
      $result[$value["_id"]["tag"]][strtotime($value["_id"]["month"] . '-1')] = abs($value["value"]["amount"]);
    }
    return $result;
  }

  /**
   * _getOperationsSummary 
   * 
   * @param mixed $mapFunction 
   * @access protected
   * @return void
   */
  protected function _getOperationsSummary($mapFunction)
  {
    $reduceFunction = "
        function (key, values) {
          var sum = 0;
          for (var i = 0; i < values.length; i++) {
            if (values[i] < 0) {
              sum += values[i];
            }
          }
          return sum;
        }";
    $query = $this->createQueryBuilder();
    $query->sort("dateOperation", "asc")
      ->map($mapFunction)
      ->reduce($reduceFunction);
    $rows = $query->getQuery()->execute();

    $newRows = array();
    foreach ($rows as $key => $row) {
      $newRows[$key]["value"] = abs($row["value"]);
      $newRows[$key]["_id"]   = strtotime($row["_id"] . "-01") * 1000;
    }
    return $newRows;
  }
}
