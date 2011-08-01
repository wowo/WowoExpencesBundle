<?php
namespace Wowo\ExpencesBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wowo\ExpencesBundle\Document\Operation;

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
    $dm = $this->get('doctrine.odm.mongodb.document_manager');
    $rows = $dm->getRepository("Wowo\ExpencesBundle\Document\Operation")->getTagsOperationsSummary();
    $current = $this->get("request")->attributes->get("_route");

    return $this->render(
      "Expences:Stats:tagsGraph.twig.html",
      array("rows" => $rows, "title" => "Monthly tags summary", "current" => $current)
    );
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
    );
    foreach ($items as $key => $item) {
      if ($item[0] == $current) {
        unset($items[$key]);
      }
    }

    return $this->render(
      "Expences:Stats:menu.twig.html",
      array("items" => $items)
    );
  }

  /**
   * monthlyGraphAction 
   * 
   * @access public
   * @return void
   */
  public function monthlyGraphAction()
  {
    $dm = $this->get('doctrine.odm.mongodb.document_manager');
    $rows = $dm->getRepository("Wowo\ExpencesBundle\Document\Operation")->getMonthlyOperationsSummary();
    $current = $this->get("request")->attributes->get("_route");

    return $this->render(
      "Expences:Stats:statsGraph.twig.html",
      array("rows" => $rows, "title" => "Monthly expences graph", "current" => $current)
    );
  }

  /**
   * yearlyGraphAction 
   * 
   * @access public
   * @return void
   */
  public function yearlyGraphAction()
  {
    $dm = $this->get('doctrine.odm.mongodb.document_manager');
    $rows = $dm->getRepository("Wowo\ExpencesBundle\Document\Operation")->getYearlyOperationsSummary();
    $current = $this->get("request")->attributes->get("_route");

    return $this->render(
      "Expences:Stats:statsGraph.twig.html",
      array("rows" => $rows, "title" => "Yearly expences graph", "current" => $current)
    );
  }

  /**
   * Gets operations sums for tags per months (runs map-reduce on mongodb)
   * 
   * @access protected
   * @return array
   */
}
