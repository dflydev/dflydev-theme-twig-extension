Theme Twig Extension
====================

A [Theme][00] extension for [Twig][01].


Installation
------------

Through [Composer][00] as [dflydev/theme-twig-extension][01].


Usage
-----

```php
use Dflydev\Twig\Extension\Theme\ThemeTwigExtension;
$themeTwigExtension = new ThemeTwigExtension(
    $themeProvider,
    $pathMapper,
    $urlGenerator
);
$twig->addExtension($themeTwigExtension);
```

Once enabled, Theme resources can be referenced by:

    {{ theme_resource('css/main.css') }}

If `css/main.css` is accessible directly via the Theme instances docroot
(as determined by the `PathMapperInterface`) then a URL to the public
resource will be used.

If `css/main.css` is *not* accessible directly, the `UrlGeneratorInterface`
implementation will be used to generate a URL to the embedded Theme
controllers.

### Controller Requirements

The following named controllers are required to be defined or the fallback
functionality will fail.

 * **_dflydev_typed_theme_handler**:
   Expects three params, `type`, `name`, and the `resource`. Name can be
   changed by calling `setTypedRouteName()`.

   Example:

   ```php
   <?php
   $app->get('/_theme_typed/{type}/{name}/public/{resource}', function($type, $name, $resource) use ($app)  {
       // do something to handle the theme request and return the
       // contents of the theme resource manually.
   })
   ->assert('name', '.+')
   ->assert('resource', '.+')
   ->bind('_dflydev_typed_theme_handler');
   ```
 * **_dflydev_theme_handler**:
   Expects two params, `name` and the `resource`. Name can be changed by calling
   `setTypedRouteName()`.

   Example:

   ```php
   <?php
   $app->get('/_theme/{name}/public/{resource}', function($name, $resource) use ($app) {
       // do something to handle the theme request and return the
       // contents of the theme resource manually.
   })
   ->assert('name', '.+')
   ->assert('resource', '.+')
   ->bind('_dflydev_theme_handler');
   ```

License
-------

MIT, see LICENSE.


Community
---------

If you have questions or want to help out, join us in the
[#dflydev][#dflydev] channel on irc.freenode.net.

[00]: http://github.com/dflydev/dflydev-theme
[01]: http://twig.sensiolabs.org/
[02]: http://getcomposer.org
[03]: https://packagist.org/packages/dflydev/ant-path-matcher

[#dflydev]: irc://irc.freenode.net/#dflydev
