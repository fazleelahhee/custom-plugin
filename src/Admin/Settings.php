<?php

namespace WPCPlugin\Admin;

use WPCPlugin\Plugin;

/**
 * Class Settings
 *
 * Generate setting page in WordPress admin,
 * allow user to update custom endpoint
 *
 * @package WPCPlugin\Admin
 * phpcs:disable WordPress.Security.EscapeOutput.UnsafePrintingFunction
 */
class Settings
{
    /**
     * Option Key for custom endpopint
     *
     * @var string
     */
    public const WPCPLUGIN_CUSTOM_ENDPOINT_KEY = 'wpcplugin_custom-endpoint';

    public function init(): void
    {
        add_action("admin_menu", [$this, "addMenuItems"]);
        add_action("admin_init", [$this, "displayOptions"]);

        add_filter('plugin_action_links_custom-plugin/wpc-plugin.php', [$this, 'addPluginActionLink']);
    }

    /**
     * Filter callback method for plugins settings page
     *
     * @param array $links
     * @return array
     */
    public function addPluginActionLink(array $links): array
    {
        $url = esc_url(add_query_arg(
            'page',
            'wpc-plugin',
            get_admin_url() . 'admin.php'
        ));

        $fronendEndPoint = esc_url('/' . get_option(self::WPCPLUGIN_CUSTOM_ENDPOINT_KEY));

        // Create the link.
        $settingsLink = "<a href='$url'>" . __('Settings', "wpc-plugin") . '</a>';
        $frontendLink = "<a href='$fronendEndPoint' target='_blank'>"
            . __('View Custom Endpoint', "wpc-plugin") . '</a>';
        // Adds the link to the end of the array.
        array_push(
            $links,
            $settingsLink
        );

        array_push(
            $links,
            $frontendLink
        );
        return $links;
    }

    public function addMenuItems(): void
    {
        add_menu_page(
            __("WPCPlugin Settings", "wpc-plugin"),
            __("WPCP", "wpc-plugin"),
            "manage_options",
            WPC_PLUGIN_NAME,
            [$this, "settingsPage"],
            "",
            100
        );
    }

    public function settingsPage(): void
    {
        ?>
        <div class="wrap">
            <div id="icon-options-general" class="icon32"></div>
            <h1><?php esc_attr_e("WPCPlugins Settings", "wpc-plugin")?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields("wpcp_section");
                do_settings_sections(WPC_PLUGIN_NAME);
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function displayOptions(): void
    {
        add_settings_section("wpcp_section", "Endpoint Settings", "__return_false", WPC_PLUGIN_NAME);
        add_settings_field(
            self::WPCPLUGIN_CUSTOM_ENDPOINT_KEY,
            __("Endpoint", "wpc-plugin"),
            [$this, "displayEndpointInput"],
            WPC_PLUGIN_NAME,
            "wpcp_section"
        );
        register_setting("wpcp_section", self::WPCPLUGIN_CUSTOM_ENDPOINT_KEY);
    }

    public function displayEndpointInput(): void
    {
        ?>
        <input type="text"
               name="<?php echo esc_attr(self::WPCPLUGIN_CUSTOM_ENDPOINT_KEY); ?>"
               id="<?php echo esc_attr(self::WPCPLUGIN_CUSTOM_ENDPOINT_KEY); ?>"
               value="<?php echo esc_attr(get_option(self::WPCPLUGIN_CUSTOM_ENDPOINT_KEY)); ?>" />
        <p><a href="/<?php echo esc_attr(get_option(self::WPCPLUGIN_CUSTOM_ENDPOINT_KEY)); ?>"
              target="_blank"> <?php _e("Visit Custom Endpoint", 'wpc-plugin') ?></a></p>
        <?php
    }
}