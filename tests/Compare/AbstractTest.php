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

namespace MezzioTest\BootstrapForm\LaminasView\View\Helper\Compare;

use Laminas\ServiceManager\Exception\ContainerModificationsNotAllowedException;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\HelperPluginManager;
use Laminas\View\Renderer\RendererInterface;
use Mezzio\BootstrapForm\LaminasView\View\Helper\ConfigProvider;
use Mezzio\Helper\ServerUrlHelper as BaseServerUrlHelper;
use Mezzio\LaminasView\HelperPluginManagerFactory;
use Mezzio\LaminasView\LaminasViewRenderer;
use Mezzio\LaminasView\LaminasViewRendererFactory;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementFactory;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use Mimmi20\LaminasView\Helper\PartialRenderer\Helper\PartialRendererFactory;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function file_get_contents;
use function sprintf;
use function str_replace;

use const PHP_EOL;

/**
 * Base class for navigation view helper tests
 */
abstract class AbstractTest extends TestCase
{
    protected ServiceManager $serviceManager;

    /**
     * Path to files needed for test
     */
    protected string $files;

    /**
     * Class name for view helper to test
     */
    protected string $helperName;

    /**
     * Prepares the environment before running a test
     *
     * @throws ContainerModificationsNotAllowedException
     */
    protected function setUp(): void
    {
        $cwd = __DIR__;

        // read navigation config
        $this->files = $cwd . '/_files';

        $sm = $this->serviceManager = new ServiceManager();
        $sm->setAllowOverride(true);

        $sm->setFactory(HelperPluginManager::class, HelperPluginManagerFactory::class);
        $sm->setFactory(HtmlElementInterface::class, HtmlElementFactory::class);
        $sm->setFactory(RendererInterface::class, PartialRendererFactory::class);

        $sm->setFactory(
            'config',
            static fn (): array => [
                'view_helpers' => (new ConfigProvider())->getViewHelperConfig(),
            ]
        );
        $sm->setFactory(LaminasViewRenderer::class, LaminasViewRendererFactory::class);
        //$sm->setFactory(BaseServerUrlHelper::class, InvokableFactory::class);

        $sm->setAllowOverride(false);
    }

    /**
     * Returns the contens of the expected $file
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    protected function getExpected(string $file): string
    {
        $content = file_get_contents($this->files . '/expected/' . $file);

        static::assertIsString($content, sprintf('could not load file %s', $this->files . '/expected/' . $file));

        return str_replace(["\r\n", "\n", "\r", '##lb##'], ['##lb##', '##lb##', '##lb##', PHP_EOL], $content);
    }
}
