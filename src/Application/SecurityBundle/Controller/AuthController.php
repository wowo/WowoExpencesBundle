<?php
namespace Application\SecurityBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\SecurityContext;

/**
 * AuthController 
 * 
 * @uses Controller
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class AuthController extends Controller
{
  /**
   * loginAction 
   * 
   * @access public
   * @return void
   */
  public function loginAction()
  {
      // get the error if any (works with forward and redirect -- see below)
      if ($this->get("request")->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
          $error = $this->get("request")->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
      } else {
          $error = $this->get("request")->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
      }

      return $this->render("SecurityBundle:Auth:login.twig.html", array(
          // last username entered by the user
          "last_username" => $this->get("request")->getSession()->get(SecurityContext::LAST_USERNAME),
          "error"         => $error,
      ));
  }

  /**
   * loggedAction 
   * 
   * @access public
   * @return void
   */
  public function loggedAction()
  {
    if ($this->get("security.context")->getUser()) {
      return $this->render("SecurityBundle:Auth:logged.twig.html", array(
        "username" => $this->get("security.context")->getUser()->getUsername(),
      ));
    } else {
      return $this->createResponse("");
    }
  }
}
