<?php
namespace Application\ExpencesBundle\Form;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\ChoiceField;
use Symfony\Component\Form\FileField;
use Symfony\Component\Validator\ValidatorInterface;

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
class Upload extends Form
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
    $this->add(new FileField("file", array("secret" => md5(time()))));
    $this->add(new ChoiceField("type", array("choices" => array(
      "mbank_credit" => "mBank - kredytowa",
      "mbank_currentAccount" => "mBank - ROR"))));
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

