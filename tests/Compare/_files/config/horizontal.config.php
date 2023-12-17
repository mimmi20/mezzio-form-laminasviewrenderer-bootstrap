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

namespace Calculator;

use Laminas\Form\Element\Button;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Radio;
use Laminas\Form\Form;

return [
    'type' => Form::class,
    'options' => [
        'layout' => \Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form::LAYOUT_HORIZONTAL,
        'row_attributes' => ['class' => 'mb-3'],
        'col_attributes' => ['class' => 'col-sm-10'],
        'label_col_attributes' => ['class' => 'col-sm-2'],
    ],
    'elements' => [
        [
            'spec' => [
                'type' => Email::class,
                'name' => 'inputEmail3',
                'options' => ['label' => 'Email'],
                'attributes' => ['id' => 'inputEmail3'],
            ],
        ],
        [
            'spec' => [
                'type' => Password::class,
                'name' => 'inputPassword3',
                'options' => ['label' => 'Password'],
                'attributes' => ['id' => 'inputPassword3'],
            ],
        ],
        [
            'spec' => [
                'type' => Radio::class,
                'name' => 'gridRadios',
                'options' => [
                    'label' => 'Address',
                    'label_col_attributes' => ['class' => 'pt-0'],
                    'value_options' => [
                        [
                            'value' => 'option1',
                            'label' => 'First radio',
                            'attributes' => ['id' => 'gridRadios1'],
                        ],
                        [
                            'value' => 'option2',
                            'label' => 'Second radio',
                            'attributes' => ['id' => 'gridRadios2'],
                        ],
                        [
                            'value' => 'option3',
                            'label' => 'Third disabled radio',
                            'attributes' => [
                                'id' => 'gridRadios3',
                                'disabled' => true,
                            ],
                        ],
                    ],
                ],
                'attributes' => ['id' => 'gridRadios'],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'gridCheck1',
                'options' => [
                    'label' => 'Example checkbox',
                    'col_attributes' => ['class' => 'offset-sm-2'],
                    'use_hidden_element' => false,
                ],
                'attributes' => ['id' => 'gridCheck1'],
            ],
        ],
        [
            'spec' => [
                'type' => Button::class,
                'name' => 'button',
                'options' => [
                    'label' => 'Sign in',
                    'col_attributes' => ['class' => 'col-12'],
                ],
                'attributes' => [
                    'type' => 'submit',
                    'class' => 'btn btn-primary',
                ],
            ],
        ],
    ],
];
