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

namespace Mezzio\BootstrapForm\LaminasView\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\HelperPluginManager;
use Mimmi20\Form\Element\Links\Links;
use Mimmi20\Form\Element\Paragraph\Paragraph;

use function assert;
use function is_object;
use function method_exists;

final class FormElement extends AbstractHelper implements FormElementInterface
{
    use FormTrait;

    /**
     * Instance map to view helper
     *
     * @var array<string, string>
     */
    private array $classMap = [
        Element\Button::class => 'formButton',
        Element\Captcha::class => 'formCaptcha',
        Element\Csrf::class => 'formHidden',
        Element\Collection::class => 'formCollection',
        Element\DateTimeSelect::class => 'formDateTimeSelect',
        Element\DateSelect::class => 'formDateSelect',
        Element\MonthSelect::class => 'formMonthSelect',
        Links::class => 'formLinks',
        Paragraph::class => 'formParagraph',
    ];

    /**
     * Type map to view helper
     *
     * @var array<string, string>
     */
    private array $typeMap = [
        'checkbox' => 'formCheckbox',
        'color' => 'formColor',
        'date' => 'formDate',
        'datetime' => 'formDatetime',
        'datetime-local' => 'formDatetimeLocal',
        'email' => 'formEmail',
        'file' => 'formFile',
        'hidden' => 'formHidden',
        'image' => 'formImage',
        'month' => 'formMonth',
        'multi_checkbox' => 'formMultiCheckbox',
        'number' => 'formNumber',
        'password' => 'formPassword',
        'radio' => 'formRadio',
        'range' => 'formRange',
        'reset' => 'formReset',
        'search' => 'formSearch',
        'select' => 'formSelect',
        'submit' => 'formSubmit',
        'tel' => 'formTel',
        'text' => 'formText',
        'textarea' => 'formTextarea',
        'time' => 'formTime',
        'url' => 'formUrl',
        'week' => 'formWeek',
    ];

    /**
     * Default helper name
     */
    private string $defaultHelper = FormElementInterface::DEFAULT_HELPER;

    private HelperPluginManager $helperPluginManager;

    public function __construct(HelperPluginManager $helperPluginManager)
    {
        $this->helperPluginManager = $helperPluginManager;
    }

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @return self|string
     *
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws InvalidArgumentException
     */
    public function __invoke(?ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Render an element
     *
     * Introspects the element type and attributes to determine which
     * helper to utilize when rendering.
     *
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws InvalidArgumentException
     */
    public function render(ElementInterface $element): string
    {
        $renderedInstance = $this->renderInstance($element);

        if (null !== $renderedInstance) {
            return $renderedInstance;
        }

        $renderedType = $this->renderType($element);

        if (null !== $renderedType) {
            return $renderedType;
        }

        return $this->renderHelper($this->defaultHelper, $element);
    }

    /**
     * Set default helper name
     */
    public function setDefaultHelper(string $name): self
    {
        $this->defaultHelper = $name;

        return $this;
    }

    /**
     * Set default helper name
     */
    public function getDefaultHelper(): string
    {
        return $this->defaultHelper;
    }

    /**
     * Add form element type to plugin map
     */
    public function addType(string $type, string $plugin): self
    {
        $this->typeMap[$type] = $plugin;

        return $this;
    }

    /**
     * Add instance class to plugin map
     */
    public function addClass(string $class, string $plugin): self
    {
        $this->classMap[$class] = $plugin;

        return $this;
    }

    /**
     * Render element by helper name
     *
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws InvalidArgumentException
     */
    private function renderHelper(string $name, ElementInterface $element): string
    {
        $helper = $this->helperPluginManager->get($name);

        assert(is_object($helper));

        if ($helper instanceof FormIndentInterface || method_exists($helper, 'setIndent')) {
            $helper->setIndent($this->getIndent());
        }

        if ($helper instanceof FormRenderInterface || method_exists($helper, 'render')) {
            return $helper->render($element);
        }

        throw new InvalidArgumentException('the element does not support the render function');
    }

    /**
     * Render element by instance map
     *
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws InvalidArgumentException
     */
    private function renderInstance(ElementInterface $element): ?string
    {
        foreach ($this->classMap as $class => $pluginName) {
            if ($element instanceof $class) {
                return $this->renderHelper($pluginName, $element);
            }
        }

        return null;
    }

    /**
     * Render element by type map
     *
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws InvalidArgumentException
     */
    private function renderType(ElementInterface $element): ?string
    {
        $type = $element->getAttribute('type');

        if (isset($this->typeMap[$type])) {
            return $this->renderHelper($this->typeMap[$type], $element);
        }

        return null;
    }
}
