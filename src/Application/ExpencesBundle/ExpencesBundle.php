<?php

namespace Application\ExpencesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ExpencesBundle extends Bundle
{
  public function getNamespace()
  {
    return __NAMESPACE__;
  }

  public function getPath()
  {
    return __DIR__;
  }
}
