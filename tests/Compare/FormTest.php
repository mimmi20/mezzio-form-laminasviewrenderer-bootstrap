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

namespace Mimmi20Test\Mezzio\BootstrapForm\LaminasView\View\Helper\Compare;

use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Factory;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\HelperPluginManager;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormCollectionInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormRowInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Exception;
use Psr\Container\ContainerExceptionInterface;

use function assert;
use function trim;

final class FormTest extends AbstractTestCase
{
    /**
     * @return array<int, array{config: non-empty-string, template: non-empty-string, indent: string, messages: array<string, array<int, string>>}>
     *
     * @throws void
     */
    public static function providerTests(): array
    {
        return [
            [
                'config' => 'vertical.config.php',
                'template' => 'vertical.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'vertical2.config.php',
                'template' => 'vertical2.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'vertical.floating2.config.php',
                'template' => 'vertical.floating2.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'vertical.card.config.php',
                'template' => 'vertical.card.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'vertical.floating.config.php',
                'template' => 'vertical.floating.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'vertical.floating.card.config.php',
                'template' => 'vertical.floating.card.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'horizontal.config.php',
                'template' => 'horizontal.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'horizontal2.config.php',
                'template' => 'horizontal2.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'horizontal.collection.config.php',
                'template' => 'horizontal.collection.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'horizontal.element-group.config.php',
                'template' => 'horizontal.element-group.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'horizontal.floating.config.php',
                'template' => 'horizontal.floating.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'inline.config.php',
                'template' => 'inline.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'inline.floating.config.php',
                'template' => 'inline.floating.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'horizontal.hr.config.php',
                'template' => 'horizontal.hr.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'horizontal.hr2.config.php',
                'template' => 'horizontal.hr2.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'vertical.hr.config.php',
                'template' => 'vertical.hr.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'horizontal.hr.card.config.php',
                'template' => 'horizontal.hr.card.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'horizontal.hr.card2.config.php',
                'template' => 'horizontal.hr.card2.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'vertical.hr.card.config.php',
                'template' => 'vertical.hr.card.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'vertical.hr.card2.config.php',
                'template' => 'vertical.hr.card2.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'horizontal.phv.config.php',
                'template' => 'horizontal.phv.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'horizontal.phv2.config.php',
                'template' => 'horizontal.phv2.html',
                'indent' => '<!-- -->',
                'messages' => [],
            ],
            [
                'config' => 'horizontal.rs.config.php',
                'template' => 'horizontal.rs.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'horizontal.rs.config.php',
                'template' => 'horizontal.rs.messages.html',
                'indent' => '',
                'messages' => [
                    'gebdatum' => ['too young'],
                    'anag' => ['value not in group'],
                ],
            ],
            [
                'config' => 'vertical.floating.rs.config.php',
                'template' => 'vertical.floating.rs.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'vertical.floating.rs.config.php',
                'template' => 'vertical.floating.rs.messages.html',
                'indent' => '',
                'messages' => [
                    'gebdatum' => ['too young'],
                    'anag' => ['value not in group'],
                ],
            ],
            [
                'config' => 'vertical.rs.config.php',
                'template' => 'vertical.rs.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'vertical.rs.config.php',
                'template' => 'vertical.rs.messages.html',
                'indent' => '',
                'messages' => [
                    'gebdatum' => ['too young'],
                    'anag' => ['value not in group'],
                ],
            ],
            [
                'config' => 'vertical.admin.config.php',
                'template' => 'vertical.admin.html',
                'indent' => '',
                'messages' => [],
            ],
            [
                'config' => 'vertical.admin2.config.php',
                'template' => 'vertical.admin2.html',
                'indent' => '',
                'messages' => [],
            ],
        ];
    }

    /**
     * @param array<string, array<int, string>> $messages
     *
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    #[DataProvider('providerTests')]
    public function testForm(string $config, string $template, string $indent, array $messages): void
    {
        $form = (new Factory())->createForm(require '_files/config/' . $config);

        $expected = $this->getExpected('form/' . $template);

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $row        = $plugin->get(FormRowInterface::class);
        $collection = $plugin->get(FormCollectionInterface::class);

        assert($row instanceof FormRowInterface);
        assert($collection instanceof FormCollectionInterface);

        $helper = new Form($collection, $row);

        if ($indent !== '') {
            $helper->setIndent($indent);
        }

        if ($messages !== []) {
            $form->setMessages($messages);
        }

        self::assertSame($expected, trim($helper->render($form)));
    }
}
