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
use Laminas\Form\Exception\InvalidArgumentException;

interface FormHiddenInterface extends FormIndentInterface
{
    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @return self|string
     *
     * @throws InvalidArgumentException
     * @throws DomainException
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     */
    public function __invoke(ElementInterface | null $element = null);

    /**
     * Render a form <input> element from the provided $element
     *
     * @throws DomainException
     */
    public function render(ElementInterface $element): string;
}
