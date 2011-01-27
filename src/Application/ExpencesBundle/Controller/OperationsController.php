<?php
namespace Application\ExpencesBundle\Controller;
use Application\ExpencesBundle\Form\Operation as OperationForm;
use Application\ExpencesBundle\Form\OperationTag;
use Application\ExpencesBundle\Document\Operation;
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
   * Displays all operations
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
    $tpl = "ExpencesBundle:Operations:index.twig.html";
    return $this->render($tpl, array("operations" => $operations, "search" => $search, "tag" => $tag));
  }

  /**
   * Manages tags of given operation
   * 
   * @param mixed $operationId 
   * @access public
   * @return void
   */
  public function manageTagsAction($operationId)
  {
    $dm = $this->get('doctrine.odm.mongodb.document_manager');
    $operation = $dm->find('Application\ExpencesBundle\Document\Operation', $operationId);
    $form      = new OperationTag("operation", $operation, $this->get("validator"));
    if ($this->get("request")->getMethod() == "POST") {
        $form->bind($this->get("request")->request->get("operation"));
        if ($form->isValid()) {
          $form->process($operation, $dm);
          return $this->redirect($this->generateUrl('operations'));
        }
    }

    $tpl = "ExpencesBundle:Operations:manageTags.twig.html";
    return $this->render($tpl, array("operation" => $operation, "form" => $form));
  }

  /**
   * newAction 
   * 
   * @access public
   * @return void
   */
  public function newAction()
  {
    $operation = new Operation();
    $operation->dateOperation = new \DateTime("now");
    $form = new OperationForm("operation", $operation, $this->get("validator"));
    if ($this->get("request")->getMethod() == "POST") {
        $form->bind($this->get("request")->request->get("operation"));
        if ($form->isValid()) {
          $operation = $form->getData();
          $operation->type = "Operacja wprowadzona przez użytkownika";
          $dm = $this->get('doctrine.odm.mongodb.document_manager');
          $dm->persist($operation);
          $dm->flush();
          return $this->redirect($this->generateUrl('operations'));
        }
    }

    $tpl = "ExpencesBundle:Operations:new.twig.html";
    return $this->render($tpl, array("form" => $form));
  }
}
