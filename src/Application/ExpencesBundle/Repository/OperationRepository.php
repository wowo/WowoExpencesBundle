<?php
namespace Application\ExpencesBundle\Repository;
use Doctrine\ODM\MongoDB\DocumentRepository;

class OperationRepository extends DocumentRepository
{

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
