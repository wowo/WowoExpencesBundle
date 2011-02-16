<?php
namespace Application\ExpencesBundle\Document;
use Symfony\Component\Security\Core\User\AccountInterface;

/**
 * Use 
 * 
 * @mongodb:Document(collection="users")
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class User implements AccountInterface
{
  /**
   * @mongodb:Id
   */
  public $id;
  /**
   * @mongodb:String
   */
  public $username;

  /**
   * @mongodb:String
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

  function equals(AccountInterface $account)
  {
    return $this->username == $account->username;
  }
}

