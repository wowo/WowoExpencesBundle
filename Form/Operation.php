<?php
namespace Wowo\ExpencesBundle\Form;
use Wowo\ExpencesBundle\Document\Operation as OperationDocument;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\DateField;
use Symfony\Component\Form\CollectionField;
use Symfony\Component\Validator\ValidatorInterface;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Operation 
 * 
 * @uses Form
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class Operation extends Form
{
  /**
   * __construct 
   * 
   * @param mixed $name 
   * @param mixed $data 
   * @param ValidatorInterface $validator 
   * @param array $options 
   * @access public
   * @return void
   */
  public function __construct($name, $data = null, ValidatorInterface $validator = null, array $options = array())
  {
    parent::__construct($name, $data, $validator, $options);
    $this->add(new Textfield("description"));
    $this->add(new Textfield("price"));
    $this->add(new DateField("date"));
    $this->add(new TextField("tags_values"));
  }

  /**
   * process 
   * 
   * @param OperationDocument $operation 
   * @param DocumentManager $dm 
   * @access public
   * @return void
   */
  public function process(OperationDocument $operation, DocumentManager $dm)
  {
    $fields = $this->get("tags")->getFields();
    $tags   = array();
    foreach ($fields as $field) {
      if (trim($field->getData()) != "") {
        $tags[] = $field->getData();
      }
    }
    $operation->tags = array_unique($tags);
    $dm->flush();
  }
}

