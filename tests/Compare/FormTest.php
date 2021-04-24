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
use Mezzio\BootstrapForm\LaminasView\View\Helper\Form;
use PHPUnit\Framework\Exception;
use Psr\Container\ContainerExceptionInterface;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function trim;

final class FormTest extends AbstractTest
{
    /**
     * @throws ContainerExceptionInterface
     */
    protected function setUp(): void
    {
        parent::setUp();

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        $this->helper = $plugin->get(Form::class);
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testVerticalForm(): void
    {
        $form = (new Factory())->createForm(require '_files/config/vertical.config.php');

        $expected = $this->getExpected('form/vertical.html');

        self::assertSame($expected, trim($this->helper->render($form)));
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testHorizontalForm(): void
    {
        $form = (new Factory())->createForm(require '_files/config/horizontal.config.php');

        $expected = $this->getExpected('form/horizonal.html');

        self::assertSame($expected, trim($this->helper->render($form)));
    }

//    public function testHrForm(): void
//    {
//        self::markTestSkipped();
//        $form = (new Factory())->createForm(require '_files/config/default.hr.config.php');
//
//        $expected = $this->getExpected('form/hr.html');
//
//        self::assertSame($expected, trim($this->helper->render($form)));
//    }
//
//    public function testPhvForm(): void
//    {
//        self::markTestSkipped();
//        $form = (new Factory())->createForm(require '_files/config/default.phv.config.php');
//
//        $expected = $this->getExpected('form/phv.html');
//
//        self::assertSame($expected, trim($this->helper->render($form)));
//    }
//
//    public function testRsForm(): void
//    {
//        self::markTestSkipped();
//        $form = (new Factory())->createForm(require '_files/config/default.rs.config.php');
//
//        $expected = $this->getExpected('form/rs.html');
//
//        self::assertSame($expected, trim($this->helper->render($form)));
//    }
//
//    public function testTierForm(): void
//    {
//        self::markTestSkipped();
//        $form = (new Factory())->createForm(require '_files/config/default.tier.config.php');
//
//        $expected = $this->getExpected('form/tier.html');
//
//        self::assertSame($expected, trim($this->helper->render($form)));
//    }
//
//    public function testUnfallForm(): void
//    {
//        self::markTestSkipped();
//        $form = (new Factory())->createForm(require '_files/config/default.unfall.config.php');
//
//        $expected = $this->getExpected('form/unfall.html');
//
//        self::assertSame($expected, trim($this->helper->render($form)));
//    }
}
