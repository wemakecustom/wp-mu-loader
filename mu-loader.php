<?php

function mu_loader_plugins_files()
{
  
    if (defined('WP_INSTALLING') && WP_INSTALLING === true) {
      // Do nothing during installation
      return array();
    }
  
    // Cache plugins
    $plugins = get_site_transient('mu_loader_plugins');

    if ($plugins !== false) {
        // Validate plugins still exist
        // If not, invalidate cache
        foreach ($plugins as $plugin_file) {
            if (!is_readable(WPMU_PLUGIN_DIR . '/' . $plugin_file)) {
                $plugins = false;
                break;
            }
        }
    }

    if ($plugins === false) {
        if (!function_exists('get_plugins')) {
            // get_plugins is not included by default
            require ABSPATH . 'wp-admin/includes/plugin.php';
        }

        // Invalid cache
        $plugins = array();
        foreach (get_plugins('/../mu-plugins') as $plugin_file => $data) {
            if (dirname($plugin_file) != '.' && dirname($plugin_file) != 'mu-loader') { // skip files directly at root
                $plugins[] = $plugin_file;
            }
        }

        set_site_transient('mu_loader_plugins', $plugins);
    }

    return $plugins;
}

add_action('muplugins_loaded', function(){
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/wp-admin/plugins.php') !== false) {
        // delete cache when viewing plugins page in /wp-admin/
        delete_site_transient('mu_loader_plugins');
    }

    foreach (mu_loader_plugins_files() as $plugin_file) {
        require_once WPMU_PLUGIN_DIR . '/' . $plugin_file;
    }
});

/**
 * Add rows for each subplugin under this plugin when listing mu-plugins in wp-admin
 */
add_action('admin_init', function() {

    add_action('after_plugin_row_mu-require.php', function() {
        $table = new WP_Plugins_List_Table;

        foreach (mu_loader_plugins_files() as $plugin_file) {
            $plugin_data = get_plugin_data(WPMU_PLUGIN_DIR . '/' . $plugin_file, false);

            if (empty($plugin_data['Name'])) {
                $plugin_data['Name'] = $plugin_file;
            }
            $plugin_data['Name'] = "&nbsp;&nbsp;&nbsp;&nbsp;+&nbsp;&nbsp;" . $plugin_data['Name'];

            $table->single_row(array($plugin_file, $plugin_data));
        }
    });
});
