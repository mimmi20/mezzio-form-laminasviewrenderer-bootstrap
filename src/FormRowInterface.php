<?php
/**
 * This file is part of the mimmi20/mezzio-form-laminasviewrenderer-bootstrap package.
 *
 * Copyright (c) 2021-2024, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper;

use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\I18n\Exception\RuntimeException;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Exception\InvalidArgumentException;

interface FormRowInterface extends FormIndentInterface
{
    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @return self|string
     *
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws DomainException
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     */
    public function __invoke(
        ElementInterface | null $element = null,
        string | null $labelPosition = null,
        bool | null $renderErrors = null,
        string | null $partial = null,
    );

    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param string|null $labelPosition
     *
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws DomainException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\RuntimeException
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue.NullabilityTypeMissing
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function render(ElementInterface $element, $labelPosition = null): string;
}
