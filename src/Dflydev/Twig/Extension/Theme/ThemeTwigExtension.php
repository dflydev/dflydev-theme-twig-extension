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

use Dflydev\Theme\ResourceUrlGenerator\ResourceUrlGeneratorInterface;

/**
 * Theme Twig Extension.
 *
 * @author Beau Simensen <beau@dflydev.com>
 */
class ThemeTwigExtension extends \Twig_Extension
{
    /**
     * Constructor
     *
     * @param ResourceUrlGeneratorInterface $resourceUrlGenerator
     */
    public function __construct(ResourceUrlGeneratorInterface $resourceUrlGenerator)
    {
        $this->resourceUrlGenerator = $resourceUrlGenerator;
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'theme_resource' => new \Twig_Function_Method($this, 'generateResourceUrl'),
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
     * Generate a URL for a Theme's resource
     *
     * @param string $resource
     *
     * @return string
     */
    public function generateResourceUrl($resource)
    {
        return $this->resourceUrlGenerator->generateResourceUrl($resource);
    }
}
