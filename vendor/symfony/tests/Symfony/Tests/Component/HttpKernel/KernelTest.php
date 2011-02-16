<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Tests\Component\HttpKernel;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\Loader\LoaderInterface;

class KernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLocateResourceThrowsExceptionWhenNameIsNotValid()
    {
        $this->getKernelForInvalidLocateResource()->locateResource('foo');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testLocateResourceThrowsExceptionWhenNameIsUnsafe()
    {
        $this->getKernelForInvalidLocateResource()->locateResource('@foo/../bar');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLocateResourceThrowsExceptionWhenBundleDoesNotExist()
    {
        $this->getKernelForInvalidLocateResource()->locateResource('@foo/config/routing.xml');
    }

    public function testLocateResourceReturnsTheFirstThatMatches()
    {
        $kernel = $this->getKernel();
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue(array($this->getBundle(__DIR__.'/Fixtures/Bundle1'))))
        ;

        $this->assertEquals(__DIR__.'/Fixtures/Bundle1/foo.txt', $kernel->locateResource('@foo/foo.txt'));
    }

    public function testLocateResourceReturnsTheFirstThatMatchesWithParent()
    {
        $parent = $this->getBundle(__DIR__.'/Fixtures/Bundle1', null, 'ParentAABundle');
        $child = $this->getBundle(__DIR__.'/Fixtures/Bundle2', 'ParentAABundle', 'ChildAABundle');

        $kernel = $this->getKernel();
        $kernel
            ->expects($this->any())
            ->method('getBundle')
            ->will($this->returnValue(array($child, $parent)))
        ;

        $this->assertEquals(__DIR__.'/Fixtures/Bundle2/foo.txt', $kernel->locateResource('@foo/foo.txt'));
        $this->assertEquals(__DIR__.'/Fixtures/Bundle1/bar.txt', $kernel->locateResource('@foo/bar.txt'));
    }

    public function testLocateResourceReturnsTheAllMatches()
    {
        $kernel = $this->getKernel();
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue(array($this->getBundle(__DIR__.'/Fixtures/Bundle1'), $this->getBundle(__DIR__.'/Fixtures/Bundle2'))))
        ;

        $this->assertEquals(array(__DIR__.'/Fixtures/Bundle1/foo.txt', __DIR__.'/Fixtures/Bundle2/foo.txt'), $kernel->locateResource('@foo/foo.txt', null, false));
    }

    public function testLocateResourceReturnsAllMatchesBis()
    {
        $kernel = $this->getKernel();
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue(array($this->getBundle(__DIR__.'/Fixtures/Bundle1'), $this->getBundle(__DIR__.'/foobar'))))
        ;

        $this->assertEquals(array(__DIR__.'/Fixtures/Bundle1/foo.txt'), $kernel->locateResource('@foo/foo.txt', null, false));
    }

    public function testLocateResourceIgnoresDirOnNonResource()
    {
        $kernel = $this->getKernel();
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue(array($this->getBundle(__DIR__.'/Fixtures/Bundle1'))))
        ;

        $this->assertEquals(__DIR__.'/Fixtures/Bundle1/foo.txt', $kernel->locateResource('@foo/foo.txt', __DIR__.'/Fixtures'));
    }

    public function testLocateResourceReturnsTheDirOneForResources()
    {
        $kernel = $this->getKernel();

        $this->assertEquals(__DIR__.'/Fixtures/foo/foo.txt', $kernel->locateResource('@foo/Resources/foo.txt', __DIR__.'/Fixtures'));
    }

    public function testLocateResourceReturnsTheDirOneForResourcesAndBundleOnes()
    {
        $kernel = $this->getKernel();
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue(array($this->getBundle(__DIR__.'/Fixtures/Bundle1'))))
        ;

        $this->assertEquals(array(__DIR__.'/Fixtures/foo/foo.txt', __DIR__.'/Fixtures/Bundle1/Resources/foo.txt'), $kernel->locateResource('@foo/Resources/foo.txt', __DIR__.'/Fixtures', false));
    }

    public function testLocateResourceOnDirectories()
    {
        $kernel = $this->getKernel();

        $this->assertEquals(__DIR__.'/Fixtures/foo/', $kernel->locateResource('@foo/Resources/', __DIR__.'/Fixtures'));
        $this->assertEquals(__DIR__.'/Fixtures/foo/', $kernel->locateResource('@foo/Resources', __DIR__.'/Fixtures'));

        $kernel
            ->expects($this->any())
            ->method('getBundle')
            ->will($this->returnValue(array($this->getBundle(__DIR__.'/Fixtures/Bundle1'))))
        ;

        $this->assertEquals(__DIR__.'/Fixtures/Bundle1/Resources/', $kernel->locateResource('@foo/Resources/'));
        $this->assertEquals(__DIR__.'/Fixtures/Bundle1/Resources/', $kernel->locateResource('@foo/Resources/'));
    }

    public function testInitializeBundles()
    {
        $parent = $this->getBundle(null, null, 'ParentABundle');
        $child = $this->getBundle(null, 'ParentABundle', 'ChildABundle');

        $kernel = $this->getKernel();
        $kernel
            ->expects($this->once())
            ->method('registerBundles')
            ->will($this->returnValue(array($parent, $child)))
        ;
        $kernel->initializeBundles();

        $map = $kernel->getBundleMap();
        $this->assertEquals(array($child, $parent), $map['ParentABundle']);
    }

    public function testInitializeBundlesSupportInheritanceCascade()
    {
        $grandparent = $this->getBundle(null, null, 'GrandParentBBundle');
        $parent = $this->getBundle(null, 'GrandParentBBundle', 'ParentBBundle');
        $child = $this->getBundle(null, 'ParentBBundle', 'ChildBBundle');

        $kernel = $this->getKernel();
        $kernel
            ->expects($this->once())
            ->method('registerBundles')
            ->will($this->returnValue(array($grandparent, $parent, $child)))
        ;

        $kernel->initializeBundles();

        $map = $kernel->getBundleMap();
        $this->assertEquals(array($child, $parent, $grandparent), $map['GrandParentBBundle']);
        $this->assertEquals(array($child, $parent), $map['ParentBBundle']);
        $this->assertEquals(array($child), $map['ChildBBundle']);
    }

    /**
     * @expectedException \LogicException
     */
    public function testInitializeBundlesThrowsExceptionWhenAParentDoesNotExists()
    {
        $child = $this->getBundle(null, 'FooBar', 'ChildCBundle');

        $kernel = $this->getKernel();
        $kernel
            ->expects($this->once())
            ->method('registerBundles')
            ->will($this->returnValue(array($child)))
        ;
        $kernel->initializeBundles();
    }

    public function testInitializeBundlesSupportsArbitaryBundleRegistrationOrder()
    {
        $grandparent = $this->getBundle(null, null, 'GrandParentCCundle');
        $parent = $this->getBundle(null, 'GrandParentCCundle', 'ParentCCundle');
        $child = $this->getBundle(null, 'ParentCCundle', 'ChildCCundle');

        $kernel = $this->getKernel();
        $kernel
            ->expects($this->once())
            ->method('registerBundles')
            ->will($this->returnValue(array($parent, $grandparent, $child)))
        ;

        $kernel->initializeBundles();

        $map = $kernel->getBundleMap();
        $this->assertEquals(array($child, $parent, $grandparent), $map['GrandParentCCundle']);
        $this->assertEquals(array($child, $parent), $map['ParentCCundle']);
        $this->assertEquals(array($child), $map['ChildCCundle']);
    }

    /**
     * @expectedException \LogicException
     */
    public function testInitializeBundlesThrowsExceptionWhenABundleIsDirectlyExtendedByTwoBundles()
    {
        $parent = $this->getBundle(null, null, 'ParentCBundle');
        $child1 = $this->getBundle(null, 'ParentCBundle', 'ChildC1Bundle');
        $child2 = $this->getBundle(null, 'ParentCBundle', 'ChildC2Bundle');

        $kernel = $this->getKernel();
        $kernel
            ->expects($this->once())
            ->method('registerBundles')
            ->will($this->returnValue(array($parent, $child1, $child2)))
        ;
        $kernel->initializeBundles();
    }

    /**
     * @expectedException \LogicException
     */
    public function testInitializeBundleThrowsExceptionWhenRegisteringTwoBundlesWithTheSameName()
    {
        $fooBundle = $this->getBundle(null, null, 'FooBundle', 'DuplicateName');
        $barBundle = $this->getBundle(null, null, 'BarBundle', 'DuplicateName');
       
        $kernel = $this->getKernel();
        $kernel
            ->expects($this->once())
            ->method('registerBundles')
            ->will($this->returnValue(array($fooBundle, $barBundle)))
        ;
        $kernel->initializeBundles();      
    }

    protected function getBundle($dir = null, $parent = null, $className = null, $bundleName = null)
    {
        $bundle = $this
            ->getMockBuilder('Symfony\Tests\Component\HttpKernel\BundleForTest')
            ->setMethods(array('getNormalizedPath', 'getParent', 'getName'))
            ->disableOriginalConstructor()
        ;

        if ($className) {
            $bundle->setMockClassName($className);
        }

        $bundle = $bundle->getMockForAbstractClass();

        $bundle
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(is_null($bundleName) ? get_class($bundle) : $bundleName))
        ;

        $bundle
            ->expects($this->any())
            ->method('getNormalizedPath')
            ->will($this->returnValue(strtr($dir, '\\', '/')))
        ;
        
        $bundle
            ->expects($this->any())
            ->method('getParent')
            ->will($this->returnValue($parent))
        ;
        
        return $bundle;
    }

    protected function getKernel()
    {
        return $this
            ->getMockBuilder('Symfony\Tests\Component\HttpKernel\KernelForTest')
            ->setMethods(array('getBundle', 'registerBundles'))
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    protected function getKernelForInvalidLocateResource()
    {
        return $this
            ->getMockBuilder('Symfony\Component\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass()
        ;
    }
}

class KernelForTest extends Kernel
{
    public function getBundleMap()
    {
        return $this->bundleMap;
    }

    public function registerRootDir()
    {
    }

    public function registerBundles()
    {
    }

    public function registerBundleDirs()
    {
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }

    public function initializeBundles()
    {
        parent::initializeBundles();
    }
}

abstract class BundleForTest implements BundleInterface
{
    // We can not extend Symfony\Component\HttpKernel\Bundle\Bundle as we want to mock getName() which is final
}