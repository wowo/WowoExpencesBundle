<?php
namespace Application\ExpencesBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * CRUD + tagging for operations
 * 
 * @uses Controller
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class OperationsController extends Controller
{
  /**
   * indexAction 
   * 
   * @access public
   * @return void
   */
  public function indexAction()
  {
    $dm = $this->get('doctrine.odm.mongodb.document_manager');
    $query = $dm->createQueryBuilder('Application\ExpencesBundle\Document\Operation');
    $query->sort("dateOperation", "desc");
    $search = $this->get("request")->query->get("query");
    $tag    = $this->get("request")->query->get("tag");
    if ($search) {
      $query->field("description")->equals(new \MongoRegex(sprintf("/%s/i", $search)));
    }
    if ($tag) {
      $query->field("tags")->in(array($tag));
    }
    $operations = $query->getQuery()->execute();
    
    return $this->render('ExpencesBundle:Operations:index.twig.html', array("operations" => $operations, "search" => $search, "tag" => $tag));
  }
}
