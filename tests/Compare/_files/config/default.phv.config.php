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

use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;

return [
    'type' => Form::class,
    'options' => [
        'layout' => \Mezzio\BootstrapForm\LaminasView\View\Helper\Form::LAYOUT_HORIZONTAL,
    ],
    'attributes' => [
        'method' => 'post',
        'accept-charset' => 'utf-8',
        'action' => '/calculator/phv/1/input/hdi8atj8urkn4vmp93uuc4s9ov?subid=A-00-000',
        'class' => 'form input-form js-help has-help has-preloader js-form-validation-base col-12 js-input-form-init',
        'data-show-arrow' => 'left',
        'id' => 'phv-form',
    ],
    'elements' => [
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'single',
                'options' => [
                    'label' => 'Tarifauswahl',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'Familie/Lebensgemeinschaft mit Kinder' => 'Familie/Lebensgemeinschaft mit Kind(ern)',
                        'Familie/Lebensgemeinschaft ohne Kinder' => 'Familie/Lebensgemeinschaft ohne Kinder',
                        'Single ohne Kinder' => 'Single ohne Kinder',
                        'Single mit Kinder' => 'Single mit Kind(ern)',
                    ],
                ],
                'attributes' => [
                    'id' => 'single',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'plz',
                'options' => [
                    'label' => 'PLZ des Antragsstellers<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'plz',
                    'class' => 'form-control form-control-input form-control-short js-special-zip-message',
                    'required' => 'required',
                    'pattern' => '^\d{5}$',
                    'minlength' => '5',
                    'maxlength' => '5',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'beamte',
                'options' => [
                    'label' => 'Tarifgruppe',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'Normal' => 'Normal',
                        'öffentl. Dienst' => 'öffentlicher Dienst',
                        'ÖD mit Dienst-HP (nur Lehrer)' => 'ÖD mit Dienst-HP (nur Lehrer)',
                    ],
                ],
                'attributes' => [
                    'id' => 'beamte',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'gebdatum',
                'options' => [
                    'label' => 'Geburtsdatum des Versicherungsnehmers<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'gebdatum',
                    'class' => 'form-control form-control-input datepicker js-datepicker',
                    'placeholder' => 'TT.MM.JJJJ',
                    'required' => 'required',
                    'data-date-format' => 'de',
                    'data-date-format-message' => 'Bitte geben Sie ein korrektes Geburtsdatum an!',
                    'data-min-age' => '18y',
                    'data-min-age-message' => 'Sie sind leider zu jung, um eine Versicherung abzuschließen.',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'ausfall',
                'options' => [
                    'label' => 'Ausfalldeckung',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'ausfall',
                    'class' => 'form-control form-control-select toggle-trigger',
                    'data-toggle-modus' => 'hide',
                    'data-toggle-value' => 'nein',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'delikt',
                'options' => [
                    'label' => 'Deliktunfähige Kinder unter 7 Jahre mitversichern?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'delikt',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'Hundn',
                'options' => [
                    'label' => 'Hund mitversichern? (kein Kampfhund!)',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        '0' => '0',
                        '1' => '1 Hund',
                        '2' => '2 Hunde',
                        '3' => '3 Hunde',
                        '4' => '4 Hunde',
                        '5' => '5 Hunde',
                    ],
                ],
                'attributes' => [
                    'id' => 'Hundn',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'Rasse_Tier1',
                'options' => [
                    'label' => 'Rasse Hund 1 eingeben<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'Rasse_Tier1',
                    'class' => 'form-control form-control-input js-pet-autocomplete',
                    'required' => 'required',
                    'pattern' => '[A-Za-z_äâàÄÂÀöÖüÜßÉÊÈéèêç()\s.-]{2,}',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'Rasse_Tier2',
                'options' => [
                    'label' => 'Rasse Hund 2 eingeben<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'Rasse_Tier2',
                    'class' => 'form-control form-control-input js-pet-autocomplete',
                    'required' => 'required',
                    'pattern' => '[A-Za-z_äâàÄÂÀöÖüÜßÉÊÈéèêç()\s.-]{2,}',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'Rasse_Tier3',
                'options' => [
                    'label' => 'Rasse Hund 3 eingeben<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'Rasse_Tier3',
                    'class' => 'form-control form-control-input js-pet-autocomplete',
                    'required' => 'required',
                    'pattern' => '[A-Za-z_äâàÄÂÀöÖüÜßÉÊÈéèêç()\s.-]{2,}',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'Rasse_Tier4',
                'options' => [
                    'label' => 'Rasse Hund 4 eingeben<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'Rasse_Tier4',
                    'class' => 'form-control form-control-input js-pet-autocomplete',
                    'required' => 'required',
                    'pattern' => '[A-Za-z_äâàÄÂÀöÖüÜßÉÊÈéèêç()\s.-]{2,}',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'Rasse_Tier5',
                'options' => [
                    'label' => 'Rasse Hund 5 eingeben<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'Rasse_Tier5',
                    'class' => 'form-control form-control-input js-pet-autocomplete',
                    'required' => 'required',
                    'pattern' => '[A-Za-z_äâàÄÂÀöÖüÜßÉÊÈéèêç()\s.-]{2,}',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'mischling_hund1',
                'options' => [
                    'label' => 'Ist Hund 1 ein Mischling?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'mischling_hund1',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'mischling_hund2',
                'options' => [
                    'label' => 'Ist Hund 2 ein Mischling?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'mischling_hund2',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'mischling_hund3',
                'options' => [
                    'label' => 'Ist Hund 3 ein Mischling?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'mischling_hund3',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'mischling_hund4',
                'options' => [
                    'label' => 'Ist Hund 4 ein Mischling?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'mischling_hund4',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'mischling_hund5',
                'options' => [
                    'label' => 'Ist Hund 5 ein Mischling?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'mischling_hund5',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'Rasse_Tier1a',
                'options' => [
                    'label' => 'Mischlingsrasse Hund 1<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'Rasse_Tier1a',
                    'class' => 'form-control form-control-input js-pet-autocomplete',
                    'required' => 'required',
                    'pattern' => '[A-Za-z_äâàÄÂÀöÖüÜßÉÊÈéèêç()\s.-]{2,}',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'Rasse_Tier2a',
                'options' => [
                    'label' => 'Mischlingsrasse Hund 2<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'Rasse_Tier2a',
                    'class' => 'form-control form-control-input js-pet-autocomplete',
                    'required' => 'required',
                    'pattern' => '[A-Za-z_äâàÄÂÀöÖüÜßÉÊÈéèêç()\s.-]{2,}',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'Rasse_Tier3a',
                'options' => [
                    'label' => 'Mischlingsrasse Hund 3<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'Rasse_Tier3a',
                    'class' => 'form-control form-control-input js-pet-autocomplete',
                    'required' => 'required',
                    'pattern' => '[A-Za-z_äâàÄÂÀöÖüÜßÉÊÈéèêç()\s.-]{2,}',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'Rasse_Tier4a',
                'options' => [
                    'label' => 'Mischlingsrasse Hund 4<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'Rasse_Tier4a',
                    'class' => 'form-control form-control-input js-pet-autocomplete',
                    'required' => 'required',
                    'pattern' => '[A-Za-z_äâàÄÂÀöÖüÜßÉÊÈéèêç()\s.-]{2,}',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'Rasse_Tier5a',
                'options' => [
                    'label' => 'Mischlingsrasse Hund 5<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'Rasse_Tier5a',
                    'class' => 'form-control form-control-input js-pet-autocomplete',
                    'required' => 'required',
                    'pattern' => '[A-Za-z_äâàÄÂÀöÖüÜßÉÊÈéèêç()\s.-]{2,}',
                ],
            ],
        ],
        'laufzeit' => [
            'spec' => [
                'type' => Select::class,
                'name' => 'laufzeit',
                'options' => [
                    'label' => 'Laufzeit',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        '1' => '1 Jahr',
                        '3' => '3 Jahre',
                        '5' => '5 Jahre',
                    ],
                ],
                'attributes' => [
                    'id' => 'laufzeit',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'vorvers5',
                'options' => [
                    'label' => 'Bestand in den letzten 5 Jahren eine Vorversicherung?<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        '' => '-- Bitte wählen --',
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'vorvers5',
                    'class' => 'form-control form-control-select',
                    'required' => 'required',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'schaeden5',
                'options' => [
                    'label' => 'Schäden in den letzten 5 Jahren<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        '' => '-- Bitte wählen --',
                        '0' => '0',
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                    ],
                ],
                'attributes' => [
                    'id' => 'schaeden5',
                    'class' => 'form-control form-control-select',
                    'required' => 'required',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'kombirabatte',
                'options' => [
                    'label' => 'Kombirabatte mit berechnen?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'kombirabatte',
                    'class' => 'form-control form-control-select toggle-trigger',
                    'data-toggle-modus' => 'show',
                    'data-toggle-value' => 'ja',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrPHV',
                'options' => [
                    'label' => 'Privathaftpflicht',
                    'label_attributes' => ['class' => 'col-sm text-sm-right check-label'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => [
                    'id' => 'KrPHV',
                    'class' => 'form-check-input',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrTIE',
                'options' => [
                    'label' => 'Tierhalterhaftpflicht',
                    'label_attributes' => ['class' => 'col-sm text-sm-right check-label'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => [
                    'id' => 'KrTIE',
                    'class' => 'form-check-input',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrHUG',
                'options' => [
                    'label' => 'Haus-Grundbesitzer Haftpflicht',
                    'label_attributes' => ['class' => 'col-sm text-sm-right check-label'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => [
                    'id' => 'KrHUG',
                    'class' => 'form-check-input',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrOEL',
                'options' => [
                    'label' => 'Gewässerschaden/Öltank',
                    'label_attributes' => ['class' => 'col-sm text-sm-right check-label'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => [
                    'id' => 'KrOEL',
                    'class' => 'form-check-input',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrWG',
                'options' => [
                    'label' => 'Wohngebäude',
                    'label_attributes' => ['class' => 'col-sm text-sm-right check-label'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => [
                    'id' => 'KrWG',
                    'class' => 'form-check-input',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrWGGLS',
                'options' => [
                    'label' => 'Wohngebäude-Glas',
                    'label_attributes' => ['class' => 'col-sm text-sm-right check-label'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => [
                    'id' => 'KrWGGLS',
                    'class' => 'form-check-input',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrHR',
                'options' => [
                    'label' => 'Hausrat',
                    'label_attributes' => ['class' => 'col-sm text-sm-right check-label'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => [
                    'id' => 'KrHR',
                    'class' => 'form-check-input',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrHRGLS',
                'options' => [
                    'label' => 'Hausrat-Glas',
                    'label_attributes' => ['class' => 'col-sm text-sm-right check-label'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => [
                    'id' => 'KrHRGLS',
                    'class' => 'form-check-input',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrUNF',
                'options' => [
                    'label' => 'Unfall',
                    'label_attributes' => ['class' => 'col-sm text-sm-right check-label'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => [
                    'id' => 'KrUNF',
                    'class' => 'form-check-input',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrRS',
                'options' => [
                    'label' => 'Rechtsschutz',
                    'label_attributes' => ['class' => 'col-sm text-sm-right check-label'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => [
                    'id' => 'KrRS',
                    'class' => 'form-check-input',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Radio::class,
                'name' => 'zusatzfragen',
                'options' => [
                    'label' => 'weitere Fragen',
                    'value_options' => [
                        'nein' => [
                            'value' => 'nein',
                            'label' => 'Ich verzichte auf die Beantwortung weiterer Fragen und wähle aus dem Vergleich einen Tarif, der meinen Bedarf erfüllt.',
                            'attributes' => [
                                'id' => 'zusatzfragen_nein',
                                'class' => 'form-check-input form-radio-input',
                                'required' => 'required',
                            ],
                            'label_attributes' => [
                                'class' => 'form-radio js-gtm-event',
                                'for' => 'zusatzfragen_nein',
                                'data-event-type' => 'click',
                                'data-event-category' => 'versicherung',
                                'data-event-action' => 'no additional questions',
                                'data-event-label' => 'phv',
                            ],
                        ],
                        'ja' => [
                            'value' => 'ja',
                            'label' => 'Ich möchte weitere Angaben zum gewünschten Versicherungsschutz machen. Es werden dann nur Tarife angezeigt, welche die Vorgaben erfüllen.',
                            'attributes' => [
                                'id' => 'zusatzfragen_ja',
                                'class' => 'form-check-input form-radio-input',
                                'required' => 'required',
                            ],
                            'label_attributes' => [
                                'class' => 'form-radio js-gtm-event',
                                'for' => 'zusatzfragen_ja',
                                'data-event-type' => 'click',
                                'data-event-category' => 'versicherung',
                                'data-event-action' => 'additional questions requested',
                                'data-event-label' => 'phv',
                            ],
                        ],
                    ],
                ],
                'attributes' => ['id' => 'zusatzfragen'],
            ],
        ],
        'vs_v' => [
            'spec' => [
                'type' => Select::class,
                'name' => 'vs_v',
                'options' => [
                    'label' => 'Mindest-Versicherungssumme Personen/Sachschäden',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'o3000000' => [
                            'value' => '3000000',
                            'label' => 'mind. 3 Mio EUR  (AK Empfehlung)',
                        ],
                        'o5000000' => [
                            'value' => '5000000',
                            'label' => 'mind. 5 Mio EUR',
                        ],
                        'o10000000' => [
                            'value' => '10000000',
                            'label' => 'mind. 10 Mio EUR',
                        ],
                    ],
                ],
                'attributes' => [
                    'id' => 'vs_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'vsm_v',
                'options' => [
                    'label' => 'Mindest-Versicherungssumme für Mietsachschäden',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        '300000' => 'mind. 300.000 EUR (AK Empfehlung)',
                        '500000' => 'mind. 500.000 EUR',
                        '1000000' => 'mind. 1 Mio EUR',
                        '3000000' => 'mind. 3 Mio EUR',
                        '0' => 'unter 500.000 EUR',
                    ],
                ],
                'attributes' => [
                    'id' => 'vsm_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'vsv_v',
                'options' => [
                    'label' => 'Mindest-Versicherungssumme für Vermögensschäden',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        '50000' => 'mind. 50.000 EUR (AK Empfehlung)',
                        '100000' => 'mind. 100.000 EUR',
                        '1000000' => 'mind. 1 Mio EUR',
                        '10000' => 'unter 50.000 EUR',
                    ],
                ],
                'attributes' => [
                    'id' => 'vsv_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'best_v',
                'options' => [
                    'label' => 'Best-Leistungsgarantie',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'best_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'sgwe_v',
                'options' => [
                    'label' => 'Selbstgenutztes Wohneigentum',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        '0 kein Wohneigentum' => 'kein Wohneigentum',
                        '1 Wohnung' => 'Wohnung',
                        '1 EFH' => 'Einfamilienhaus',
                        '2 ZFH' => 'Zweifamilienhaus',
                        '4' => 'Mehrfamilienhaus bis 4 Wohnungen',
                        '100' => 'Mehrfamilienhaus über 4 Wohnungen',
                    ],
                ],
                'attributes' => [
                    'id' => 'sgwe_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'ew_v',
                'options' => [
                    'label' => 'Eigentumswohnungen in Deutschland, vermietet',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'ew_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'unbgru_v',
                'options' => [
                    'label' => 'Unbebautes Grundstück',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'unbgru_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'einlw_v',
                'options' => [
                    'label' => 'Einliegerwohnung vermietet im selbstbewohnten Haus',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'einlw_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'fewoi_v',
                'options' => [
                    'label' => 'Ferienhaus-/Wohnung im Inland selbstgenutzt',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'fewoi_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'fewoa_v',
                'options' => [
                    'label' => 'Wohnung / Ferienwohnung / Ferienhaus im europ. Ausland ohne Vermietung',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'fewoa_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'fach_v',
                'options' => [
                    'label' => 'Fachpraktischer Unterricht (Laborarbeiten)',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja (AK Empfehlung)',
                    ],
                ],
                'attributes' => [
                    'id' => 'fach_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'tier_v',
                'options' => [
                    'label' => 'Hüten eines fremden Hundes/Pferdes',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja (AK Empfehlung)',
                    ],
                ],
                'attributes' => [
                    'id' => 'tier_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'oel_v',
                'options' => [
                    'label' => 'Einschluss Öltankhaftpflicht',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'kein Tank vorhanden' => 'kein Tank vorhanden',
                        '3000' => 'bis 3.000 Liter',
                        '5000' => 'bis 5.000 Liter',
                        '10000' => 'bis 10.000 Liter',
                        '20000' => 'bis 20.000 Liter',
                        '50000' => 'bis 50.000 Liter',
                        '100000' => 'bis 100.000 Liter',
                    ],
                ],
                'attributes' => [
                    'id' => 'oel_v',
                    'class' => 'form-control form-control-select toggle-trigger',
                    'data-toggle-modus' => 'hide',
                    'data-toggle-value' => 'kein Tank vorhanden',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'oelwo_v',
                'options' => [
                    'label' => 'Wo befindet sich der Öltank?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'oberirdisch' => 'oberirdisch',
                        'unterirdisch' => 'unterirdisch',
                    ],
                ],
                'attributes' => [
                    'id' => 'oelwo_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'allm_v',
                'options' => [
                    'label' => 'Allmählichkeitsschäden',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'mind. 3 Mio (AK Empfehlung)',
                    ],
                ],
                'attributes' => [
                    'id' => 'allm_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'gewsa_v',
                'options' => [
                    'label' => 'Gewässerschaden-Risiko, z.B. Farben',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja (AK Empfehlung)',
                    ],
                ],
                'attributes' => [
                    'id' => 'gewsa_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'abwa_v',
                'options' => [
                    'label' => 'Schäden durch häusliche Abwässer',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'mind. 3 Mio (AK Empfehlung)',
                    ],
                ],
                'attributes' => [
                    'id' => 'abwa_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'inet_v',
                'options' => [
                    'label' => 'Schäden durch elektronischen Datenaustausch/Internetnutzung',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja (AK Empfehlung)',
                    ],
                ],
                'attributes' => [
                    'id' => 'inet_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'gef_v',
                'options' => [
                    'label' => 'Gefälligkeitsschäden',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'gef_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'geli_v',
                'options' => [
                    'label' => 'Gemietete oder geliehene Sachen',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'geli_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'sv_v',
                'options' => [
                    'label' => 'Schlüsselverlust, fremder privater Schlüssel (Mietwohnung)',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'sv_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'sva_v',
                'options' => [
                    'label' => 'Schlüsselverlust für Zentrale Schließanlage (keine Eigenschäden)',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'sva_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'svd_v',
                'options' => [
                    'label' => 'Schlüsselverlust von fremden Dienstschlüsseln',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'svd_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'welt_v',
                'options' => [
                    'label' => 'Weltweite Deckung gewünscht (Standard ist Europa oder EU)',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'welt_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'elt_v',
                'options' => [
                    'label' => 'Alleinstehendes Elternteil im Haushalt lebend',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'elt_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'ehr_v',
                'options' => [
                    'label' => 'Ehrenamtliche Tätigkeit',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'ehr_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'tagmu_v',
                'options' => [
                    'label' => 'Tätigkeit als Tagesmutter',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'tagmu_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'regr_v',
                'options' => [
                    'label' => 'Regressansprüche von Sozialversicherungsträgern von mitversicherten Personen',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja (AK Empfehlung)',
                    ],
                ],
                'attributes' => [
                    'id' => 'regr_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'bau_v',
                'options' => [
                    'label' => 'Bauherrenhaftpflicht am Haus oder Grundstück',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        '0' => 'nein',
                        '50000' => 'bis 50.000 EUR (AK Empfehlung)',
                        '100000' => 'bis 100.000 EUR',
                        '200000' => 'bis 200.000 EUR',
                        '1000000' => 'bis Versicherungssumme',
                    ],
                ],
                'attributes' => [
                    'id' => 'bau_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'surf_v',
                'options' => [
                    'label' => 'Eigene Surfbretter',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'surf_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'tiere_v',
                'options' => [
                    'label' => 'Besitzen Sie Hunde, Pferde, Rinder, landwirtschaftliche Tiere?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja, evtl. extra Vertrag notwendig!' => 'ja, evtl. extra Vertrag notwendig!',
                    ],
                ],
                'attributes' => [
                    'id' => 'tiere_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'wafa_v',
                'options' => [
                    'label' => 'Benutzen Sie eigene Wasserfahrzeuge?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja, extra Vertrag notwendig!' => 'ja, extra Vertrag notwendig!',
                    ],
                ],
                'attributes' => [
                    'id' => 'wafa_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'flug_v',
                'options' => [
                    'label' => 'Besitzen Sie Modellflugzeuge, Ballone oder Drachen?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja, evtl. extra Vertrag notwendig!' => 'ja, evtl. extra Vertrag notwendig!',
                    ],
                ],
                'attributes' => [
                    'id' => 'flug_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'drohne_v',
                'options' => [
                    'label' => 'Besitzen Sie privat genutzte Drohnen?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'drohne_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'jagd_v',
                'options' => [
                    'label' => 'Gehen Sie auf die Jagd?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja, evtl. extra Vertrag notwendig!' => 'ja, evtl. extra Vertrag notwendig!',
                    ],
                ],
                'attributes' => [
                    'id' => 'jagd_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'foto_v',
                'options' => [
                    'label' => 'Betreiben Sie eine Photovoltaikanlage?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja, evtl. extra Vertrag notwendig!' => 'ja, evtl. extra Vertrag notwendig!',
                    ],
                ],
                'attributes' => [
                    'id' => 'foto_v',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'rsausfall',
                'options' => [
                    'label' => 'Rechtsschutz zur Ausfalldeckung',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'rsausfall',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'selbst',
                'options' => [
                    'label' => 'Selbstbeteiligung',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => '0 €',
                        '99' => 'bis 99 €',
                        '100' => 'bis 100 €',
                        '125' => 'bis 125 €',
                        '150' => 'bis 150 €',
                        '250' => 'bis 250 €',
                    ],
                ],
                'attributes' => [
                    'id' => 'selbst',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'zahlweise',
                'options' => [
                    'label' => 'Zahlweise',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        '1' => 'jährlich',
                        '2' => 'halbjährlich',
                        '4' => 'vierteljährlich',
                        '12' => 'monatlich',
                    ],
                ],
                'attributes' => [
                    'id' => 'zahlweise',
                    'class' => 'form-control form-control-select',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'chkErstinfo',
                'options' => [
                    'use_hidden_element' => true,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => [
                    'id' => 'mrmoErstinfo',
                    'class' => 'form-check-input',
                    'required' => 'required',
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
                    'data-event-label' => 'phv',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Submit::class,
                'name' => 'recalc',
                'options' => ['label' => 'neu berechnen'],
                'attributes' => ['value' => 'neu berechnen'],
            ],
        ],
    ],
    'input_filter' => [
        'plz' => ['required' => true],
        'KrPHV' => ['required' => false],
        'KrTIE' => ['required' => false],
        'KrHUG' => ['required' => false],
        'KrOEL' => ['required' => false],
        'KrWG' => ['required' => false],
        'KrWGGLS' => ['required' => false],
        'KrHR' => ['required' => false],
        'KrHRGLS' => ['required' => false],
        'KrUNF' => ['required' => false],
        'KrRS' => ['required' => false],
        'selbst' => ['required' => false],
        'zahlweise' => ['required' => false],
    ],
];
