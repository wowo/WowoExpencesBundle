<?php
namespace Application\ExpencesBundle\Controller;
use Application\ExpencesBundle\Document\User;
use Application\ExpencesBundle\Form\Operation as OperationForm;
use Application\ExpencesBundle\Form\OperationTag;
use Application\ExpencesBundle\Form\Upload;
use Application\ExpencesBundle\Document\Operation;
use Application\ExpencesBundle\Factories\BankSummaryReader;
use Application\ExpencesBundle\Importer\Mongo as MongoImporter;
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
  const REPO = "Application\ExpencesBundle\Document\Operation";

  /**
   * Displays all operations
   * 
   * @access public
   * @return void
   */
  public function indexAction()
  {
    $dm = $this->get("doctrine.odm.mongodb.document_manager");
    $token = $this->get("security.context")->getToken();

    $search = $this->get("request")->query->get("query");
    $tag    = $this->get("request")->query->get("tag");
    $user   = $token ? $token->getUser() : null;
    if ($user instanceof User) {
      $operations = $dm->getRepository(self::REPO)->getOperationsForUser($user, $tag, $search);
    } else {
      $operations = array();
    }
    return $this->render(
      "ExpencesBundle:Operations:index.twig.html", 
      array("operations" => $operations, "search" => $search, "tag" => $tag)
    );
  }

  /**
   * Save Tags Action 
   * 
   * @access public
   * @return void
   */
  public function saveTagsAction()
  {
    $dm = $this->get('doctrine.odm.mongodb.document_manager');
    $operation = $dm->find('Application\ExpencesBundle\Document\Operation', $this->get("request")->request->get("id"));
    $operation->tags = $this->getTagsFromRequest($this->get("request"));
    $dm->persist($operation);
    $dm->flush();
    return $this->render(
      "ExpencesBundle::tags.twig.html",
      array("tags" => $operation->tags)
    );
  }

  /**
   * Gets tags from request
   * 
   * @param mixed $request 
   * @access protected
   * @return void
   */
  protected function getTagsFromRequest($request)
  {
    $tags = $request->request->get("tags");
    $tags = explode(",", $tags);
    $tags = array_map("trim", $tags);
    foreach ($tags as $key => $tag) {
      if (strlen($tag) == 0) {
        unset($tags[$key]);
      }
    }
    return $tags;
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

    return $this->render(
      "ExpencesBundle:Operations:new.twig.html",
      array("form" => $form)
    );
  }

  /**
   * uploadAction 
   * 
   * @access public
   * @return void
   */
  public function uploadAction()
  {
    $form = new Upload("upload", array(), $this->get("validator"));
    if ($this->get("request")->getMethod() == "POST") {
      $post  = $this->get("request")->request->get("upload");
      $files = $this->get("request")->files->get("upload");
      $xmlString  = file_get_contents($files["file"]["file"]->getPath());
      $types = explode("_", $post["type"]);

      $factory = new BankSummaryReader();
      $reader  = $factory->getBankSummaryReader($types[0], $types[1]);
      $operations = $reader->getOperations($xmlString);
      $importer = new MongoImporter($this->get("doctrine.odm.mongodb.document_manager"));
      $importer->importFromUpload($operations, $this->get("security.context")->getUser(), $types[0], $types[1]);
      return $this->redirect($this->generateUrl('operations'));
    }

    return $this->render(
      "ExpencesBundle:Operations:upload.twig.html",
      array("form" => $form)
    );
  }
}
