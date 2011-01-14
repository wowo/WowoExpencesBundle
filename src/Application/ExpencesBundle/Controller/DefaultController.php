<?php

namespace Application\ExpencesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ExpencesBundle:Default:index.php');
    }
}
