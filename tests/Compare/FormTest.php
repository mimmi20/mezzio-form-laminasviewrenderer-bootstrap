<?php
/**
 * This file is part of the mimmi20/mezzio-form-laminasviewrenderer-bootstrap package.
 *
 * Copyright (c) 2021-2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20Test\Mezzio\BootstrapForm\LaminasView\View\Helper\Compare;

use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Factory;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\HelperPluginManager;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormCollectionInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormRowInterface;
use PHPUnit\Framework\Exception;
use Psr\Container\ContainerExceptionInterface;

use function assert;
use function trim;

final class FormTest extends AbstractTestCase
{
    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testVerticalForm(): void
    {
        $form = (new Factory())->createForm(require '_files/config/vertical.config.php');

        $expected = $this->getExpected('form/vertical.html');

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $row        = $plugin->get(FormRowInterface::class);
        $collection = $plugin->get(FormCollectionInterface::class);

        assert($row instanceof FormRowInterface);
        assert($collection instanceof FormCollectionInterface);

        $helper = new Form($collection, $row);

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testVerticalFormAsCard(): void
    {
        $form = (new Factory())->createForm(require '_files/config/vertical.card.config.php');

        $expected = $this->getExpected('form/vertical.card.html');

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $row        = $plugin->get(FormRowInterface::class);
        $collection = $plugin->get(FormCollectionInterface::class);

        assert($row instanceof FormRowInterface);
        assert($collection instanceof FormCollectionInterface);

        $helper = new Form($collection, $row);

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testVerticalWithFloatingLabelsForm(): void
    {
        $form = (new Factory())->createForm(require '_files/config/vertical.floating.config.php');

        $expected = $this->getExpected('form/vertical.floating.html');

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $row        = $plugin->get(FormRowInterface::class);
        $collection = $plugin->get(FormCollectionInterface::class);

        assert($row instanceof FormRowInterface);
        assert($collection instanceof FormCollectionInterface);

        $helper = new Form($collection, $row);

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testVerticalAsCardWithFloatingLabelsForm(): void
    {
        $form = (new Factory())->createForm(require '_files/config/vertical.floating.card.config.php');

        $expected = $this->getExpected('form/vertical.floating.card.html');

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $row        = $plugin->get(FormRowInterface::class);
        $collection = $plugin->get(FormCollectionInterface::class);

        assert($row instanceof FormRowInterface);
        assert($collection instanceof FormCollectionInterface);

        $helper = new Form($collection, $row);

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testHorizontalForm(): void
    {
        $form = (new Factory())->createForm(require '_files/config/horizontal.config.php');

        $expected = $this->getExpected('form/horizonal.html');

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $row        = $plugin->get(FormRowInterface::class);
        $collection = $plugin->get(FormCollectionInterface::class);

        assert($row instanceof FormRowInterface);
        assert($collection instanceof FormCollectionInterface);

        $helper = new Form($collection, $row);

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testHorizontalForm2(): void
    {
        $form = (new Factory())->createForm(require '_files/config/horizontal2.config.php');

        $expected = $this->getExpected('form/horizonal2.html');

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $row        = $plugin->get(FormRowInterface::class);
        $collection = $plugin->get(FormCollectionInterface::class);

        assert($row instanceof FormRowInterface);
        assert($collection instanceof FormCollectionInterface);

        $helper = new Form($collection, $row);

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testHorizontalFormWithCollection(): void
    {
        $form = (new Factory())->createForm(require '_files/config/horizontal.collection.config.php');

        $expected = $this->getExpected('form/horizontal.collection.html');

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $row        = $plugin->get(FormRowInterface::class);
        $collection = $plugin->get(FormCollectionInterface::class);

        assert($row instanceof FormRowInterface);
        assert($collection instanceof FormCollectionInterface);

        $helper = new Form($collection, $row);

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testHorizontalFormWithElementGroup(): void
    {
        $form = (new Factory())->createForm(
            require '_files/config/horizontal.element-group.config.php',
        );

        $expected = $this->getExpected('form/horizontal.element-group.html');

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $row        = $plugin->get(FormRowInterface::class);
        $collection = $plugin->get(FormCollectionInterface::class);

        assert($row instanceof FormRowInterface);
        assert($collection instanceof FormCollectionInterface);

        $helper = new Form($collection, $row);

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testInlineForm(): void
    {
        $form = (new Factory())->createForm(require '_files/config/inline.config.php');

        $expected = $this->getExpected('form/inline.html');

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $row        = $plugin->get(FormRowInterface::class);
        $collection = $plugin->get(FormCollectionInterface::class);

        assert($row instanceof FormRowInterface);
        assert($collection instanceof FormCollectionInterface);

        $helper = new Form($collection, $row);

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testHrForm(): void
    {
        $form = (new Factory())->createForm(require '_files/config/horizontal.hr.config.php');

        $expected = $this->getExpected('form/hr.html');

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $row        = $plugin->get(FormRowInterface::class);
        $collection = $plugin->get(FormCollectionInterface::class);

        assert($row instanceof FormRowInterface);
        assert($collection instanceof FormCollectionInterface);

        $helper = new Form($collection, $row);

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testHrFormAsCard(): void
    {
        $form = (new Factory())->createForm(require '_files/config/horizontal.hr.card.config.php');

        $expected = $this->getExpected('form/hr.card.html');

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $row        = $plugin->get(FormRowInterface::class);
        $collection = $plugin->get(FormCollectionInterface::class);

        assert($row instanceof FormRowInterface);
        assert($collection instanceof FormCollectionInterface);

        $helper = new Form($collection, $row);

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testHrFormAsCard2(): void
    {
        $form = (new Factory())->createForm(require '_files/config/horizontal.hr.card2.config.php');

        $expected = $this->getExpected('form/hr.card2.html');

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $row        = $plugin->get(FormRowInterface::class);
        $collection = $plugin->get(FormCollectionInterface::class);

        assert($row instanceof FormRowInterface);
        assert($collection instanceof FormCollectionInterface);

        $helper = new Form($collection, $row);

        self::assertSame($expected, trim($helper->render($form)));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testPhvForm(): void
    {
        $form = (new Factory())->createForm(require '_files/config/horizontal.phv.config.php');

        $expected = $this->getExpected('form/phv.html');

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $row        = $plugin->get(FormRowInterface::class);
        $collection = $plugin->get(FormCollectionInterface::class);

        assert($row instanceof FormRowInterface);
        assert($collection instanceof FormCollectionInterface);

        $helper = new Form($collection, $row);

        self::assertSame($expected, trim($helper->render($form)));
    }
}
