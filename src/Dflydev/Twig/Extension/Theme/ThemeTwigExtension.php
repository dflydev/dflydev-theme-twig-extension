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

use Dflydev\Theme\ThemeProviderInterface;
use Dflydev\Theme\PathMapper\PathMapperInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Theme Twig Extension.
 *
 * @author Beau Simensen <beau@dflydev.com>
 */
class ThemeTwigExtension extends \Twig_Extension
{
    /**
     * Typed Route Name
     *
     * The name of the route to be used when generating fallback URL
     * for non-typed themes.
     *
     * @var string
     */
    protected $typedRouteName = '_dflydev_typed_theme_handler';

    /**
     * Route Name
     *
     * The name of the route to be used when generating fallback URL
     * for typed themes.
     *
     * @var string
     */
    protected $routeName = '_dflydev_theme_handler';

    /**
     * Constructor
     *
     * @param ThemeProviderInterface $themeProvider Theme Provider
     * @param PathMapperInterface    $pathMapper    Path Mapper
     * @param UrlGeneratorInterface  $urlGenerator  URL Generator
     */
    public function __construct(ThemeProviderInterface $themeProvider, PathMapperInterface $pathMapper, UrlGeneratorInterface $urlGenerator)
    {
        $this->themeProvider = $themeProvider;
        $this->pathMapper = $pathMapper;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Set Typed Route Name
     *
     * @param string $typedRouteName
     *
     * @return ThemeTwigExtension
     */
    public function setTypedRouteName($typedRouteName)
    {
        $this->typedRouteName = $typedRouteName;

        return $this;
    }

    /**
     * Typed route name
     *
     * @return string
     */
    public function typedRouteName()
    {
        return $this->typedRouteName;
    }

    /**
     * Set Route Name
     *
     * @param string $routeName
     *
     * @return ThemeTwigExtension
     */
    public function setRouteName($routeName)
    {
        $this->routeName = $routeName;

        return $this;
    }

    /**
     * Route name
     *
     * @return string
     */
    public function routeName()
    {
        return $this->routeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'theme_resource' => new \Twig_Function_Method($this, 'generateThemeResourceUrl'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'theme';
    }

    /**
     * Generate a URL for a Theme resource
     *
     * If the actual file representing the resource exists where the path
     * mapper says it should exist then a URL is created to that location.
     * Otherwise, a fallback URL will be created using the URL Generator.
     *
     * @param string $resource
     *
     * @return string
     */
    public function generateThemeResourceUrl($resource)
    {
        $theme = $this->themeProvider->provideTheme();

        $filesystemPath = $this->pathMapper->generatePublicResourceFilesystemPathForTheme($theme, $resource);

        if (file_exists($filesystemPath)) {
            return $this->pathMapper->generatePublicResourceUrlForTheme(
                $theme,
                $resource
            );
        }

        if ($type = $theme->type()) {
            return $this->urlGenerator->generate($this->typedRouteName, array(
                'type' => $type,
                'name' => $theme->name(),
                'resource' => $resource,
            ));
        }

        return $this->urlGenerator->generate($this->routeName, array(
            'name' => $theme->name(),
            'resource' => $resource
        ));
    }
}
