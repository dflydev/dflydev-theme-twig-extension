<?php

/*
 * This file is a part of Theme Twig Extension.
 *
 * (c) Dragonfly Development Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dflydev\Twig\Extension\Theme;

/**
 * ThemeTwigExtension Test
 *
 * @author Beau Simensen <beau@dflydev.com>
 */
class ThemeTwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected function createThemeTwigExtensionWithDeps()
    {
        $resourceUrlGenerator = $this->getMock('Dflydev\Theme\ResourceUrlGenerator\ResourceUrlGeneratorInterface');

        $themeTwigExtension = new ThemeTwigExtension($resourceUrlGenerator);

        return array($themeTwigExtension, $resourceUrlGenerator);
    }

    protected function createThemeTwigExtension()
    {
        $objects = $this->createThemeTwigExtensionWithDeps();

        return $objects[0];
    }

    /**
     * Test getName()
     */
    public function testGetName()
    {
        $themeTwigExtension = $this->createThemeTwigExtension();

        $this->assertEquals('theme', $themeTwigExtension->getName());
    }

    /**
     * Test getFunctions()
     */
    public function testGetFunctions()
    {
        $themeTwigExtension = $this->createThemeTwigExtension();

        $functions = $themeTwigExtension->getFunctions();
        $this->assertArrayHasKey('theme_resource', $functions);
    }

    /**
     * Test generateResourceUrl()
     */
    public function testGenerateResourceUrl()
    {
        list($themeTwigExtension, $resourceUrlGenerator) = $this->createThemeTwigExtensionWithDeps();

        $resourceUrlGenerator
            ->expects($this->once())
            ->method('generateResourceUrl')
            ->with('css/main.css')
            ->will($this->returnValue('/url/path/to/css/main.css'));

        $this->assertEquals('/url/path/to/css/main.css', $themeTwigExtension->generateResourceUrl('css/main.css'));
    }
}
