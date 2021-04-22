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
use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\View\HelperPluginManager;

final class FormElement extends AbstractHelper
{
    public const DEFAULT_HELPER = 'formInput';

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
    private string $defaultHelper = self::DEFAULT_HELPER;

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
     *
     * @return $this
     */
    public function setDefaultHelper(string $name)
    {
        $this->defaultHelper = $name;

        return $this;
    }

    /**
     * Add form element type to plugin map
     *
     * @return $this
     */
    public function addType(string $type, string $plugin)
    {
        $this->typeMap[$type] = $plugin;

        return $this;
    }

    /**
     * Add instance class to plugin map
     *
     * @return $this
     */
    public function addClass(string $class, string $plugin)
    {
        $this->classMap[$class] = $plugin;

        return $this;
    }

    /**
     * Render element by helper name
     */
    private function renderHelper(string $name, ElementInterface $element): string
    {
        $helper = $this->helperPluginManager->get($name);

        return $helper($element);
    }

    /**
     * Render element by instance map
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
