<?php

namespace WPCPlugin;

class Install
{
    public static function activate()
    {
        update_option(Plugin::PLUGIN_REWRITE_FLUSH_KEY, 0);
    }
}
