<?php
namespace Wowo\ExpencesBundle\Document;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Use 
 * 
 * @MongoDB\Document(collection="users")
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class User implements UserInterface
{
  /**
   * @MongoDB\Id
   */
  public $id;
  /**
   * @MongoDB\String
   */
  public $username;

  /**
   * @MongoDB\String
   */
  public $password;

  public function __toString()
  {
    return $this->getUsername();
  }

  public function getRoles()
  {
    return array("ROLE_USER");
  }

  public function getPassword()
  {
    return $this->password;
  }

  public function getSalt()
  {
    return null;
  }

  public function getUsername()
  {
    return $this->username;
  }

  public function eraseCredentials()
  {
  }

  function equals(UserInterface $account)
  {
    return $this->username == $account->username;
  }
}

