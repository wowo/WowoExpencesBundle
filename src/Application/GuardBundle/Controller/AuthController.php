<?php
namespace Application\GuardBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Application\ExpencesBundle\Document\User;

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

      return $this->render("Guard:Auth:login.twig.html", array(
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
    $token = $this->get("security.context")->getToken();
    if ($token && $token->getUser() instanceof User) {
      return $this->render("Guard:Auth:logged.twig.html", array(
        "username" => $token->getUser()->getUsername(),
      ));
    } else {
      return new Response("");
    }
  }
}
