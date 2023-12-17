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

namespace Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper;

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
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     */
    public function __invoke(ElementInterface | null $element = null);

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
     *
     * @throws void
     */
    public function setDefaultHelper(string $name): self;

    /**
     * Set default helper name
     *
     * @throws void
     */
    public function getDefaultHelper(): string;

    /**
     * Add form element type to plugin map
     *
     * @throws void
     */
    public function addType(string $type, string $plugin): self;

    /**
     * Add instance class to plugin map
     *
     * @throws void
     */
    public function addClass(string $class, string $plugin): self;
}
