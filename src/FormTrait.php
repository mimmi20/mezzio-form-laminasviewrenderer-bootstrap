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

use function is_int;
use function str_repeat;

trait FormTrait
{
    private string $indent = '';

    /**
     * Set the indentation string for using in {@link render()}, optionally a
     * number of spaces to indent with
     *
     * @param int|string $indent
     */
    public function setIndent($indent): self
    {
        $this->indent = $this->getWhitespace($indent);

        return $this;
    }

    /**
     * Returns indentation
     */
    public function getIndent(): string
    {
        return $this->indent;
    }

    // Util methods:

    /**
     * Retrieve whitespace representation of $indent
     *
     * @param int|string $indent
     */
    private function getWhitespace($indent): string
    {
        if (is_int($indent)) {
            $indent = str_repeat(' ', $indent);
        }

        return (string) $indent;
    }
}
