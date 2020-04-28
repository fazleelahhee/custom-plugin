<?php

declare(strict_types=1);

namespace WPCPlugin;

use WPCPlugin\Admin\Settings;

class Install
{
    public static function activate(): void
    {
        update_option(Plugin::PLUGIN_REWRITE_FLUSH_KEY, 0);
        update_option(Settings::WPCPLUGIN_CUSTOM_ENDPOINT_KEY, 'wpcp-endpoint');
    }
}
