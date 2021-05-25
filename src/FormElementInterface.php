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

use Laminas\Form\ElementInterface;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;

interface FormElementInterface extends FormIndentInterface
{
    public const DEFAULT_HELPER = 'formInput';

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @return self|string
     *
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     */
    public function __invoke(?ElementInterface $element = null);

    /**
     * Render an element
     *
     * Introspects the element type and attributes to determine which
     * helper to utilize when rendering.
     *
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     */
    public function render(ElementInterface $element): string;

    /**
     * Set default helper name
     */
    public function setDefaultHelper(string $name): self;

    /**
     * Add form element type to plugin map
     */
    public function addType(string $type, string $plugin): self;

    /**
     * Add instance class to plugin map
     */
    public function addClass(string $class, string $plugin): self;
}
