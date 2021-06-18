<?php
/**
 * This file is part of the mimmi20/mezzio-form-laminasviewrenderer-bootstrap package.
 *
 * Copyright (c) 2021, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace MezzioTest\BootstrapForm\LaminasView\View\Helper\Compare;

use Laminas\Form\Exception\DomainException;
use Laminas\Form\Factory;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\HelperPluginManager;
use Mezzio\BootstrapForm\LaminasView\View\Helper\Form;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormCollectionInterface;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormRowInterface;
use PHPUnit\Framework\Exception;
use Psr\Container\ContainerExceptionInterface;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function trim;

final class FormTest extends AbstractTest
{
    /**
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     *
     * @coversNothing
     */
    public function testVerticalForm(): void
    {
        $form = (new Factory())->createForm(require '_files/config/vertical.config.php');

        $expected = $this->getExpected('form/vertical.html');

        $helper = new Form(
            $this->serviceManager->get(HelperPluginManager::class)->get(FormCollectionInterface::class),
            $this->serviceManager->get(HelperPluginManager::class)->get(FormRowInterface::class)
        );

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     *
     * @coversNothing
     */
    public function testVerticalWithFloatingLabelsForm(): void
    {
        $form = (new Factory())->createForm(require '_files/config/vertical.floating.config.php');

        $expected = $this->getExpected('form/vertical.floating.html');

        $helper = new Form(
            $this->serviceManager->get(HelperPluginManager::class)->get(FormCollectionInterface::class),
            $this->serviceManager->get(HelperPluginManager::class)->get(FormRowInterface::class)
        );

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     *
     * @coversNothing
     */
    public function testHorizontalForm(): void
    {
        $form = (new Factory())->createForm(require '_files/config/horizontal.config.php');

        $expected = $this->getExpected('form/horizonal.html');

        $helper = new Form(
            $this->serviceManager->get(HelperPluginManager::class)->get(FormCollectionInterface::class),
            $this->serviceManager->get(HelperPluginManager::class)->get(FormRowInterface::class)
        );

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     *
     * @coversNothing
     */
    public function testHorizontalFormWithCollection(): void
    {
        $form = (new Factory())->createForm(require '_files/config/horizontal.collection.config.php');

        $expected = $this->getExpected('form/horizontal.collection.html');

        $helper = new Form(
            $this->serviceManager->get(HelperPluginManager::class)->get(FormCollectionInterface::class),
            $this->serviceManager->get(HelperPluginManager::class)->get(FormRowInterface::class)
        );

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     *
     * @coversNothing
     */
    public function testHorizontalFormWithElementGroup(): void
    {
        $form = (new Factory())->createForm(require '_files/config/horizontal.element-group.config.php');

        $expected = $this->getExpected('form/horizontal.element-group.html');

        $helper = new Form(
            $this->serviceManager->get(HelperPluginManager::class)->get(FormCollectionInterface::class),
            $this->serviceManager->get(HelperPluginManager::class)->get(FormRowInterface::class)
        );

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     *
     * @coversNothing
     */
    public function testInlineForm(): void
    {
        $form = (new Factory())->createForm(require '_files/config/inline.config.php');

        $expected = $this->getExpected('form/inline.html');

        $helper = new Form(
            $this->serviceManager->get(HelperPluginManager::class)->get(FormCollectionInterface::class),
            $this->serviceManager->get(HelperPluginManager::class)->get(FormRowInterface::class)
        );

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     *
     * @coversNothing
     */
    public function testHrForm(): void
    {
        $form = (new Factory())->createForm(require '_files/config/default.hr.config.php');

        $expected = $this->getExpected('form/hr.html');

        $helper = new Form(
            $this->serviceManager->get(HelperPluginManager::class)->get(FormCollectionInterface::class),
            $this->serviceManager->get(HelperPluginManager::class)->get(FormRowInterface::class)
        );

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     *
     * @coversNothing
     */
    public function testPhvForm(): void
    {
        $form = (new Factory())->createForm(require '_files/config/default.phv.config.php');

        $expected = $this->getExpected('form/phv.html');

        $helper = new Form(
            $this->serviceManager->get(HelperPluginManager::class)->get(FormCollectionInterface::class),
            $this->serviceManager->get(HelperPluginManager::class)->get(FormRowInterface::class)
        );

        self::assertSame($expected, trim($helper->render($form)));
    }

//    public function testRsForm(): void
//    {
//        self::markTestSkipped();
//        $form = (new Factory())->createForm(require '_files/config/default.rs.config.php');
//
//        $expected = $this->getExpected('form/rs.html');
//
//        $helper = new Form(
//            $this->serviceManager->get(HelperPluginManager::class)->get(FormCollectionInterface::class),
//            $this->serviceManager->get(HelperPluginManager::class)->get(FormRowInterface::class)
//        );
//
//        self::assertSame($expected, trim($helper->render($form)));
//    }
//
//    public function testTierForm(): void
//    {
//        self::markTestSkipped();
//        $form = (new Factory())->createForm(require '_files/config/default.tier.config.php');
//
//        $expected = $this->getExpected('form/tier.html');
//
//        $helper = new Form(
//            $this->serviceManager->get(HelperPluginManager::class)->get(FormCollectionInterface::class),
//            $this->serviceManager->get(HelperPluginManager::class)->get(FormRowInterface::class)
//        );
//
//        self::assertSame($expected, trim($helper->render($form)));
//    }
//
//    public function testUnfallForm(): void
//    {
//        self::markTestSkipped();
//        $form = (new Factory())->createForm(require '_files/config/default.unfall.config.php');
//
//        $expected = $this->getExpected('form/unfall.html');
//
//        $helper = new Form(
//            $this->serviceManager->get(HelperPluginManager::class)->get(FormCollectionInterface::class),
//            $this->serviceManager->get(HelperPluginManager::class)->get(FormRowInterface::class)
//        );
//
//        self::assertSame($expected, trim($helper->render($form)));
//    }
}
