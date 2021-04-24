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

use Laminas\ServiceManager\Exception\ContainerModificationsNotAllowedException;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Helper\HelperInterface;
use Laminas\View\HelperPluginManager;
use Mezzio\BootstrapForm\LaminasView\View\Helper\Form;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormButton;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormButtonFactory;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormCheckbox;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormCheckboxFactory;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormCollection;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormCollectionFactory;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormElement;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormElementErrors;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormElementErrorsFactory;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormElementFactory;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormEmail;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormFactory;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormFile;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormHidden;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormLabel;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormLabelFactory;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormMultiCheckbox;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormMultiCheckboxFactory;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormPassword;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormRadio;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormRadioFactory;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormRange;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormRow;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormRowFactory;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormSelect;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormSelectFactory;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormSubmit;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormText;
use Mezzio\Helper\ServerUrlHelper as BaseServerUrlHelper;
use Mezzio\LaminasView\HelperPluginManagerFactory;
use Mezzio\LaminasView\LaminasViewRenderer;
use Mezzio\LaminasView\LaminasViewRendererFactory;
use Mezzio\LaminasViewHelper\Helper\PluginManager as LvhPluginManager;
use Mezzio\LaminasViewHelper\Helper\PluginManagerFactory as LvhPluginManagerFactory;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function file_get_contents;
use function sprintf;
use function str_replace;

use const PHP_EOL;

/**
 * Base class for navigation view helper tests
 */
abstract class AbstractTest extends TestCase
{
    protected ServiceManager $serviceManager;

    /**
     * Path to files needed for test
     */
    protected string $files;

    /**
     * Class name for view helper to test
     */
    protected string $helperName;

    /**
     * View helper
     */
    protected HelperInterface $helper;

    /**
     * Prepares the environment before running a test
     *
     * @throws ContainerModificationsNotAllowedException
     */
    protected function setUp(): void
    {
        $cwd = __DIR__;

        // read navigation config
        $this->files = $cwd . '/_files';

        $sm = $this->serviceManager = new ServiceManager();
        $sm->setAllowOverride(true);

        $sm->setFactory(HelperPluginManager::class, HelperPluginManagerFactory::class);
        $sm->setFactory(LvhPluginManager::class, LvhPluginManagerFactory::class);

        $sm->setFactory(
            'config',
            static fn (): array => [
                'view_helpers' => [
                    'aliases' => [
                        'form' => Form::class,
                        'formButton' => FormButton::class,
                        'formCheckbox' => FormCheckbox::class,
                        'formCollection' => FormCollection::class,
                        'formElement' => FormElement::class,
                        'form_element' => FormElement::class,
                        'formElementErrors' => FormElementErrors::class,
                        'formEmail' => FormEmail::class,
                        'formFile' => FormFile::class,
                        'formHidden' => FormHidden::class,
                        'formLabel' => FormLabel::class,
                        'formMultiCheckbox' => FormMultiCheckbox::class,
                        'formPassword' => FormPassword::class,
                        'formRadio' => FormRadio::class,
                        'formRange' => FormRange::class,
                        'formRow' => FormRow::class,
                        'formSelect' => FormSelect::class,
                        'formSubmit' => FormSubmit::class,
                        'formText' => FormText::class,
                    ],
                    'factories' => [
                        Form::class => FormFactory::class,
                        FormButton::class => FormButtonFactory::class,
                        FormCollection::class => FormCollectionFactory::class,
                        FormCheckbox::class => FormCheckboxFactory::class,
                        FormElement::class => FormElementFactory::class,
                        FormElementErrors::class => FormElementErrorsFactory::class,
                        FormEmail::class => InvokableFactory::class,
                        FormFile::class => InvokableFactory::class,
                        FormHidden::class => InvokableFactory::class,
                        FormLabel::class => FormLabelFactory::class,
                        FormMultiCheckbox::class => FormMultiCheckboxFactory::class,
                        FormPassword::class => InvokableFactory::class,
                        FormRadio::class => FormRadioFactory::class,
                        FormRange::class => InvokableFactory::class,
                        FormRow::class => FormRowFactory::class,
                        FormSelect::class => FormSelectFactory::class,
                        FormSubmit::class => InvokableFactory::class,
                        FormText::class => InvokableFactory::class,
                    ],
                ],
            ]
        );
        $sm->setFactory(LaminasViewRenderer::class, LaminasViewRendererFactory::class);
        $sm->setFactory(BaseServerUrlHelper::class, InvokableFactory::class);

        $sm->setAllowOverride(false);
    }

    /**
     * Returns the contens of the expected $file
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    protected function getExpected(string $file): string
    {
        $content = file_get_contents($this->files . '/expected/' . $file);

        static::assertIsString($content, sprintf('could not load file %s', $this->files . '/expected/' . $file));

        return str_replace(["\r\n", "\n", "\r", '##lb##'], ['##lb##', '##lb##', '##lb##', PHP_EOL], $content);
    }
}
