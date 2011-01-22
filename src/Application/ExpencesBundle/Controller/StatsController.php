<?php
namespace Application\ExpencesBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * StatsController 
 * 
 * @uses Controller
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class StatsController extends Controller
{
  /**
   * Shows monthly tags expences graph
   * 
   * @access public
   * @return void
   */
  public function tagsGraphAction()
  {
    $rows = $this->_getOperationsForTagsSummary();
    $tpl  = "ExpencesBundle:Stats:tagsGraph.twig.html";
    $current = $this->get("request")->attributes->get("_route");
    return $this->render($tpl, array("rows" => $rows, "title" => "Monthly tags summary", "current" => $current));
  }

  /**
   * Menu in subtitle
   * 
   * @param mixed $current 
   * @access public
   * @return void
   */
  public function menuAction($current)
  {
    $items = array(
      array("stats_monthly_graph", "Monthly graph"),
      array("stats_yearly_graph" , "Yearly graph"),
      array("tags_monthly_graph" , "Monthly tags graph"),
      array("stats_monthly"      , "Monthly stats"),
      array("stats_yearly"       , "Yearly stats"),
    );
    foreach ($items as $key => $item) {
      if ($item[0] == $current) {
        unset($items[$key]);
      }
    }
    return $this->render("ExpencesBundle:Stats:menu.twig.html", array("items" => $items));
  }
  /**
   * Gets monthly summary of the operations
   * 
   * @access public
   * @return void
   */
  public function monthlyAction()
  {
    $this->get("request")->attributes->set("current", $this->get("request")->attributes->get("_route"));
    $rows = $this->_getOperationsSummary($this->_getMonthlyMapFunction());
    $tpl  = "ExpencesBundle:Stats:statsTable.twig.html";
    $current = $this->get("request")->attributes->get("_route");
    return $this->render($tpl, array("rows" => $rows, "title" => "Monthly summary", "current" => $current));
  }

  /**
   * monthlyGraphAction 
   * 
   * @access public
   * @return void
   */
  public function monthlyGraphAction()
  {
    $rows = $this->_getOperationsSummary($this->_getMonthlyMapFunction());
    $rows = $this->_prepareForGraph($rows);
    $tpl  = "ExpencesBundle:Stats:statsGraph.twig.html";
    $current = $this->get("request")->attributes->get("_route");
    return $this->render($tpl, array("rows" => $rows, "title" => "Monthly expences graph", "current" => $current));
  }

  /**
   * Gets yearly summary of the operations
   * 
   * @access public
   * @return void
   */
  public function yearlyAction()
  {
    $rows = $this->_getOperationsSummary($this->_getYearlyMapFunction());
    $tpl  = "ExpencesBundle:Stats:statsTable.twig.html";
    $current = $this->get("request")->attributes->get("_route");
    return $this->render($tpl, array("rows" => $rows, "title" => "Yearly summary", "current" => $current));
  }

  /**
   * yearlyGraphAction 
   * 
   * @access public
   * @return void
   */
  public function yearlyGraphAction()
  {
    $rows = $this->_getOperationsSummary($this->_getYearlyMapFunction());
    $rows = $this->_prepareForGraph($rows);
    $tpl  = "ExpencesBundle:Stats:statsGraph.twig.html";
    $current = $this->get("request")->attributes->get("_route");
    return $this->render($tpl, array("rows" => $rows, "title" => "Yearly expences graph", "current" => $current));
  }

  /**
   * _prepareForGraph 
   * 
   * @param array $rows 
   * @access protected
   * @return void
   */
  protected function _prepareForGraph(\Traversable $rows)
  {
    $newRows = array();
    foreach ($rows as $key => $row) {
      $newRows[$key]["value"] = abs($row["value"]);
      $newRows[$key]["_id"]   = strtotime($row["_id"] . "-01") * 1000;
    }
    return $newRows;
  }

  /**
   * _getMonthlyMapFunction 
   * 
   * @access protected
   * @return void
   */
  protected function _getMonthlyMapFunction()
  {
    return "
      function() { 
        var month = (this.dateOperation.getMonth() + 1);
        if (month < 10) {
          month = '0' + month;
        }
        var key = this.dateOperation.getFullYear() + '-' + month;
        emit (key, this.pricePln);
      }";
  }

  /**
   * _getYearlyMapFunction 
   * 
   * @access protected
   * @return void
   */
  protected function _getYearlyMapFunction()
  {
    return "
      function() { 
        emit (this.dateOperation.getFullYear(), this.pricePln);
      }";
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
    $rows = array();
    $dm = $this->get('doctrine.odm.mongodb.document_manager');
    $query = $dm->createQueryBuilder('Application\ExpencesBundle\Document\Operation');

    $query->sort("dateOperation", "asc")
      ->map($mapFunction)
      ->reduce("
        function (key, values) {
          var sum = 0;
          for (var i = 0; i < values.length; i++) {
            if (values[i] < 0) {
              sum += values[i];
            }
          }
          return sum;
        }"
      );
    return $query->getQuery()->execute();
  }

  /**
   * Gets operations sums for tags per months (runs map-reduce on mongodb)
   * 
   * @access protected
   * @return array
   */
  protected function _getOperationsForTagsSummary()
  {
    $rows = array();
    $dm = $this->get('doctrine.odm.mongodb.document_manager');
    $query = $dm->createQueryBuilder('Application\ExpencesBundle\Document\Operation');

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
}
