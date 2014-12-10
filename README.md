# Wordpress Must-Use plugins loader

Builds a list of all plugins in the `wp-content/mu-plugins` folder and include them.

Uses the internal Wordpress function `get_plugins` for better compatibility.
In theory, any plugin could be included this way.

Sadly, `get_mu_plugins` does not have any hooks.

 * Will clear cache when visiting the plugin page in /wp-admin/.
 * Will also clear cache if a previously detected mu-plugin was deleted.

[Original idea](https://gist.github.com/lavoiesl/6302907)

[Blog post](http://blog.lavoie.sl/2013/08/wordpress-mu-plugins-subdirectory-loader.html)

## Installation

### Composer

Add this to your `composer.json`:
```json
{
    "require": {
        "wemakecustom/wp-mu-loader": "*"
    }
}
```

### Manual

Extract/clone this plugin in `wp-content/mu-plugins/mu-loader/` 

### IMPORTANT

**Copy or symlink `mu-require.php` into `wp-content/mu-plugins/`**

## Usage

Create a plugin with this in your `composer.json`: 

```json
{
    "name": "my-vendor/my-plugin",
    "type": "wordpress-muplugin",
    "keywords": ["wordpress","plugins"],
    "license": "GPL-2.0",
    "require": {
        "composer/installers": "~1.0"
    },

    "extra": {
        "installer-name": "my-plugin"
    }
}
```

The `extra.installer-name` is optional, it is to give a custom folder name in case your plugin is actually declared as `my-vendor/wp-mu-my-plugin` like this one.

`keywords` and `license` are also optional but strongly suggested.

## Extra notes

If like me, your wordpress installation is not at the root of your project, you may need to change the install path:

```json
{
    "extra": {
        "installer-paths": {
            "htdocs/wp-content/plugins/{$name}/": ["type:wordpress-plugin"],
            "htdocs/wp-content/mu-plugins/{$name}/": ["type:wordpress-muplugin"],
            "htdocs/wp-content/themes/{$name}/": ["type:wordpress-theme"]
        }
    }
}
```
