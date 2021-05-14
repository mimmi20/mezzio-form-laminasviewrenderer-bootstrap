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

use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Mimmi20\Form\Element\ElementGroup;

return [
    'type' => Form::class,
    'options' => [
        'layout' => \Mezzio\BootstrapForm\LaminasView\View\Helper\Form::LAYOUT_HORIZONTAL,
        'form-required-mark' => '<div class="mt-2 text-info-required">* Pflichtfeld</div>',
        'field-required-mark' => '<span class="text-info-required">*</span>',
    ],
    'attributes' => [
        'method' => 'post',
        'accept-charset' => 'utf-8',
        'action' => '/calculator/hr/1/input/2doqt23okbdqkgabg80guef8en?subid=A-00-000',
        'class' => 'form input-form js-help has-help has-preloader js-form-validation-base col-12 js-input-form-init',
        'data-show-arrow' => 'left',
        'id' => 'hr-form',
    ],
    'elements' => [
        [
            'spec' => [
                'type' => ElementGroup::class,
                'options' => [
                    'label' => 'Was möchten Sie versichern?',
                    'label_options' => ['always_wrap' => true],
                    'label_attributes' => ['class' => 'headline-calculator'],
                ],
                'elements' => [
                    [
                        'spec' => [
                            'type' => Select::class,
                            'name' => 'versbeginn',
                            'options' => [
                                'label' => 'Versicherungsbeginn',
                                'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                                'value_options' => [
                                    'sofort' => 'schnellstmöglich',
                                    'datum' => 'Datum angeben',
                                ],
                            ],
                            'attributes' => [
                                'id' => 'versbeginn',
                                'class' => 'form-control form-control-select toggle-trigger',
                                'data-toggle-modus' => 'show',
                                'data-toggle-value' => 'datum',
                            ],
                        ],
                    ],
                    [
                        'spec' => [
                            'type' => Text::class,
                            'name' => 'versbeginn_datum',
                            'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                            'options' => ['label' => 'Beginn am'],
                            'attributes' => [
                                'id' => 'versbeginn_datum',
                                'class' => 'form-control form-control-input datepicker js-datepicker',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Hidden::class,
                'name' => 'sToken',
            ],
        ],
        [
            'spec' => [
                'type' => Submit::class,
                'name' => 'btn_berechnen',
                'options' => ['label' => 'Berechnen'],
                'attributes' => [
                    'value' => 'Berechnen',
                    'class' => 'btn btn-default js-gtm-event',
                    'data-event-type' => 'click',
                    'data-event-category' => 'versicherung',
                    'data-event-action' => 'calculate',
                    'data-event-label' => 'hr',
                ],
            ],
        ],
    ],
    'input_filter' => [
        'versbeginn' => ['required' => false],
        'versbeginn_datum' => ['required' => false],
    ],
];
