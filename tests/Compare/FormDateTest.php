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

use Laminas\Form\Factory;
use Laminas\View\HelperPluginManager;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormDate;
use PHPUnit\Framework\Exception;
use Psr\Container\ContainerExceptionInterface;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function assert;
use function trim;

final class FormDateTest extends AbstractTest
{
    /**
     * @throws ContainerExceptionInterface
     */
    protected function setUp(): void
    {
        parent::setUp();

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        $this->helper = $plugin->get(FormDate::class);
        assert($this->helper instanceof FormDate);
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testRender(): void
    {
        $form = (new Factory())->createForm(require '_files/config/date.config.php');

        $expected = $this->getExpected('form/date.html');

        self::assertSame($expected, trim($this->helper->render($form->get('inputDate4'))));
    }
}
