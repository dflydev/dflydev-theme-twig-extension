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
class ThemeTwigExtensionText extends \PHPUnit_Framework_TestCase
{
    protected function createThemeExtensionWithDeps()
    {
        $themeProvider = $this->getMock('Dflydev\Theme\ThemeProviderInterface');
        $pathMapper = $this->getMock('Dflydev\Theme\PathMapper\PathMapperInterface');
        $urlGenerator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');

        $themeTwigExtension = new ThemeTwigExtension($themeProvider, $pathMapper, $urlGenerator);

        return array($themeTwigExtension, $themeProvider, $pathMapper, $urlGenerator);
    }

    protected function createThemeTwigExtension()
    {
        $objects = $this->createThemeExtensionWithDeps();

        return $objects[0];
    }

    /**
     * Test getting and setting route names
     */
    public function testRouteNames()
    {
        $themeTwigExtension = $this->createThemeTwigExtension();

        $this->assertEquals('_dflydev_typed_theme_handler', $themeTwigExtension->typedRouteName());
        $this->assertEquals('_dflydev_theme_handler', $themeTwigExtension->routeName());

        $themeTwigExtension->setTypedRouteName('theme_foo_typed');
        $themeTwigExtension->setRouteName('theme_foo');

        $this->assertEquals('theme_foo_typed', $themeTwigExtension->typedRouteName());
        $this->assertEquals('theme_foo', $themeTwigExtension->routeName());
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
     * Test generateThemeResourceUrl()
     */
    public function testGenerateThemeResourceUrlPathMapperHandlesRequest()
    {
        list($themeTwigExtension, $themeProvider, $pathMapper, $urlGenerator) = $this->createThemeExtensionWithDeps();

        $theme = $this->getMock('Dflydev\Theme\ThemeInterface');

        $theme
            ->expects($this->any())
            ->method('name')
            ->will($this->returnValue('blue'));

        $themeProvider
            ->expects($this->any())
            ->method('provideTheme')
            ->will($this->returnValue($theme));

        $pathMapper
            ->expects($this->once())
            ->method('generatePublicResourceFilesystemPathForTheme')
            ->with($theme, 'css/main.css')
            ->will($this->returnValue(__DIR__.'/fixtures/non-typed-theme/blue/css/main.css'));

        $pathMapper
            ->expects($this->once())
            ->method('generatePublicResourceUrlForTheme')
            ->with($theme, 'css/main.css')
            ->will($this->returnValue('/non-typed-theme/blue/css/main.css'));

        $resourceUrl = $themeTwigExtension->generateThemeResourceUrl('css/main.css');
        $this->assertEquals('/non-typed-theme/blue/css/main.css', $resourceUrl);
    }

    /**
     * Test generateThemeResourceUrl()
     */
    public function testGenerateThemeResourceUrlFallbackUrlGeneratedTyped()
    {
        list($themeTwigExtension, $themeProvider, $pathMapper, $urlGenerator) = $this->createThemeExtensionWithDeps();

        $theme = $this->getMock('Dflydev\Theme\ThemeInterface');

        $theme
            ->expects($this->any())
            ->method('name')
            ->will($this->returnValue('blue'));

        $theme
            ->expects($this->any())
            ->method('type')
            ->will($this->returnValue('admin'));

        $themeProvider
            ->expects($this->any())
            ->method('provideTheme')
            ->will($this->returnValue($theme));

        $pathMapper
            ->expects($this->once())
            ->method('generatePublicResourceFilesystemPathForTheme')
            ->with($theme, 'css/main.css')
            ->will($this->returnValue(__DIR__.'/missing-fixtures/typed-theme/blue/css/main.css'));

        $urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('_dflydev_typed_theme_handler', array(
                'type' => 'admin',
                'name' => 'blue',
                'resource' => 'css/main.css',
            ))
            ->will($this->returnValue('/typed-theme/admin/blue/css/main.css'));

        $resourceUrl = $themeTwigExtension->generateThemeResourceUrl('css/main.css');
        $this->assertEquals('/typed-theme/admin/blue/css/main.css', $resourceUrl);
    }

    /**
     * Test generateThemeResourceUrl()
     */
    public function testGenerateThemeResourceUrlFallbackUrlGeneratedNonTyped()
    {
        list($themeTwigExtension, $themeProvider, $pathMapper, $urlGenerator) = $this->createThemeExtensionWithDeps();

        $theme = $this->getMock('Dflydev\Theme\ThemeInterface');

        $theme
            ->expects($this->any())
            ->method('name')
            ->will($this->returnValue('blue'));

        $themeProvider
            ->expects($this->any())
            ->method('provideTheme')
            ->will($this->returnValue($theme));

        $pathMapper
            ->expects($this->once())
            ->method('generatePublicResourceFilesystemPathForTheme')
            ->with($theme, 'css/main.css')
            ->will($this->returnValue(__DIR__.'/missing-fixtures/typed-theme/blue/css/main.css'));

        $urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('_dflydev_theme_handler', array(
                'name' => 'blue',
                'resource' => 'css/main.css',
            ))
            ->will($this->returnValue('/non-typed-theme/blue/css/main.css'));

        $resourceUrl = $themeTwigExtension->generateThemeResourceUrl('css/main.css');
        $this->assertEquals('/non-typed-theme/blue/css/main.css', $resourceUrl);
    }
}
