<?php

require_once(__DIR__ .'/../vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php');
use Symfony\Component\ClassLoader\UniversalClassLoader;
$vendorDir = __DIR__.'/../vendor';

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony'                        => $vendorDir.'/symfony/src',
    'Application'                    => __DIR__.'/../src',
    'Doctrine\\Common\\DataFixtures' => $vendorDir.'/doctrine-data-fixtures/lib',
    'Doctrine\\Common'               => $vendorDir.'/doctrine-common/lib',
    'Doctrine\\DBAL\\Migrations'     => $vendorDir.'/doctrine-migrations/lib',
    'Doctrine\\MongoDB'              => $vendorDir.'/doctrine-mongodb/lib',
    'Doctrine\\ODM\\MongoDB'         => $vendorDir.'/doctrine-mongodb-odm/lib',
    'Doctrine\\DBAL'                 => $vendorDir.'/doctrine-dbal/lib',
    'Doctrine'                       => $vendorDir.'/doctrine/lib',
    'Zend\\Log'                      => __DIR__.'/../vendor/zend-log',
));
$loader->registerPrefixes(array(
    'Swift_'           => $vendorDir.'/swiftmailer/lib/classes',
    'Twig_'            => $vendorDir.'/twig/lib',
    'Twig_Extensions_' => $vendorDir.'/twig-extensions/lib',
));
$loader->register();
