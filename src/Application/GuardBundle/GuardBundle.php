<?php

namespace Application\GuardBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GuardBundle extends Bundle
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
