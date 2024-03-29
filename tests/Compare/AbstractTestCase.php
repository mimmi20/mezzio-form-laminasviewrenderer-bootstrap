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

use Laminas\ServiceManager\Exception\ContainerModificationsNotAllowedException;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\HelperPluginManager;
use Mezzio\LaminasView\HelperPluginManagerFactory;
use Mezzio\LaminasView\LaminasViewRenderer;
use Mezzio\LaminasView\LaminasViewRendererFactory;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementFactory;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use Mimmi20\LaminasView\Helper\PartialRenderer\Helper\PartialRendererFactory;
use Mimmi20\LaminasView\Helper\PartialRenderer\Helper\PartialRendererInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\ConfigProvider;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function sprintf;
use function str_replace;

use const PHP_EOL;

/**
 * Base class for form view helper tests
 */
abstract class AbstractTestCase extends TestCase
{
    protected ServiceManager $serviceManager;

    /**
     * Path to files needed for test
     */
    protected string $files;

    /**
     * Prepares the environment before running a test
     *
     * @throws ContainerModificationsNotAllowedException
     */
    protected function setUp(): void
    {
        $cwd = __DIR__;

        // read form config
        $this->files = $cwd . '/_files';

        $sm = $this->serviceManager = new ServiceManager();
        $sm->setAllowOverride(true);

        $sm->setFactory(HelperPluginManager::class, HelperPluginManagerFactory::class);
        $sm->setFactory(HtmlElementInterface::class, HtmlElementFactory::class);

        $config = (new ConfigProvider())();

        $sm->setService('config', $config);

        $sm->setFactory(LaminasViewRenderer::class, LaminasViewRendererFactory::class);
        $sm->setFactory(PartialRendererInterface::class, PartialRendererFactory::class);

        $sm->setAllowOverride(false);
    }

    /**
     * Returns the contens of the expected $file
     *
     * @throws Exception
     */
    protected function getExpected(string $file): string
    {
        $content = file_get_contents($this->files . '/expected/' . $file);

        static::assertIsString(
            $content,
            sprintf('could not load file %s', $this->files . '/expected/' . $file),
        );

        return str_replace(
            ["\r\n", "\n", "\r", '##lb##'],
            ['##lb##', '##lb##', '##lb##', PHP_EOL],
            $content,
        );
    }
}
