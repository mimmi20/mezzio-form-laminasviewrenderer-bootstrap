<?php

declare(strict_types = 1);

namespace Mezzio\BootstrapForm\LaminasView\View\Helper;

use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\View\Helper\FormInput;

class FormPassword extends FormInput
{
    use FormTrait;

    /**
     * Attributes valid for the input tag type="password"
     *
     * @var array<string, bool>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $validTagAttributes = [
        'name'           => true,
        'autocomplete'   => true,
        'autofocus'      => true,
        'disabled'       => true,
        'form'           => true,
        'maxlength'      => true,
        'minlength'      => true,
        'pattern'        => true,
        'placeholder'    => true,
        'readonly'       => true,
        'required'       => true,
        'size'           => true,
        'type'           => true,
        'value'          => true,
    ];

    /**
     * Determine input type to use
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    protected function getType(ElementInterface $element): string
    {
        return 'password';
    }

    /**
     * Render a form <input> element from the provided $element
     *
     * @throws DomainException
     */
    public function render(ElementInterface $element): string
    {
        $markup = parent::render($element);
        $indent = $this->getIndent();

        return $indent . $markup;
    }
}
