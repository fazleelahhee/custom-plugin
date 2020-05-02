<?php

declare(strict_types=1);

namespace WPCPlugin;

use WPCPlugin\Admin\Settings;
use WPCPlugin\Contracts\IDataSource;
use WPCPlugin\Hooks\UserListsHook;
use WPCPlugin\Hooks\UserHook;

class Plugin
{
    /**
     * Plugin Version
     *
     * @var string
     */
    public const VERSION = '1.0.0';

    /**
     * Plugin rewrite rule flush key
     * @var string
     */
    public const PLUGIN_REWRITE_FLUSH_KEY = 'wpcpplugins_permalink_flushed';

    /**
     * Custom endpoint
     *
     * @var string
     */
    protected $endpoint = '';

    /**
     * Init all actions and filters
     */
    public function init(): Plugin
    {
        Install::activate();
        $this->endpoint = get_option(Settings::WPCPLUGIN_CUSTOM_ENDPOINT_KEY);

        $this->addHooks();

        $settsion = new Settings();
        $settsion->init();

        return $this;
    }

    public function addHooks()
    {
        add_action('init', [$this, 'rewriteInit']);
        add_action('init', [$this, 'template']);

        $userListHook = new UserListsHook();
        add_action("wp_ajax_wpcplugin_user_collection", [$userListHook, 'jsonDispatch']);
        add_action("wp_ajax_nopriv_wpcplugin_user_collection", [$userListHook, 'jsonDispatch']);

        $userHook = new UserHook();
        add_action("wp_ajax_wpcplugin_user", [$userHook, "jsonDispatch"]);
        add_action("wp_ajax_nopriv_wpcplugin_user", [$userHook, "jsonDispatch"]);

        add_action('wp_head', [$this, 'addAjaxUrl']);
        add_action('wp_enqueue_scripts', [$this, 'wpcpEnqueueScripts']);
    }

    /**
     * Add rewrite rule to WP rewrites to allow view custom endpoint
     */
    public function rewriteInit(): void
    {
        $queryStr = 'index.php?pagename=' . $this->endpoint . '&user_id=$matches[1]';
        add_rewrite_rule($this->endpoint . '/?$', $queryStr, 'top');
        if (!get_option(self::PLUGIN_REWRITE_FLUSH_KEY)) {
            flush_rewrite_rules(false);
            update_option(self::PLUGIN_REWRITE_FLUSH_KEY, 1);
        }
    }

    /**
     * Add template to the custom end point.
     * Initially this function will look for template in the current active themes directory,
     * if not exists then it will use default template template from plugin.
     */
    public function template(): void
    {
        $urlPath = trim(parse_url(add_query_arg([]), PHP_URL_PATH), '/');
        if (preg_match('/' . $this->endpoint . '?$/', $urlPath)) {
            // load the file if exists in working theme folder
            $load = locate_template('template-' . WPC_PLUGIN_NAME . '.php', true);
            if (!$load) {
                //load default template
                load_template(WPC_PLUGIN_BASE_PATH . "/" . 'template-' . WPC_PLUGIN_NAME . '.php');
            }
            exit(); // just exit if template was found and loaded
        }
    }

    public function addAjaxUrl(): void
    {
        $urlPath = trim(parse_url(add_query_arg([]), PHP_URL_PATH), '/');
        /**
         * Load assets only to this custom api.
         * Its not a good idea loading assets globally,
         * its affect to design and wasting of the resources.
         */
        if (preg_match('/' . $this->endpoint . '?$/', $urlPath)) {
            ?>
            <script type="text/javascript">
                var ajaxurl = <?php echo json_encode(admin_url("admin-ajax.php")); ?>;
                var ajaxnonce = <?php echo json_encode(wp_create_nonce("ajax_nonce")); ?>;
            </script>
            <?php
        }
    }

    public function wpcpEnqueueScripts(): void
    {
        /**
         * Load assets only to this custom api.
         * Its not a good idea loading assets globally,
         * its affect to design and wasting of the resources.
         */
        $urlPath = trim(parse_url(add_query_arg([]), PHP_URL_PATH), '/');
        if (preg_match('/' . $this->endpoint . '?$/', $urlPath)) {
            $js = WPC_PLUGIN_URL . 'public/js/wpcplugin.js';
            $css = WPC_PLUGIN_URL . 'public/css/wpcplugin.css';
            $bootstrap = WPC_PLUGIN_URL . 'public/css/bootstrap.min.css';
            $version = WP_DEBUG ? time() : self::VERSION;
            wp_enqueue_script('wpcplugin_js', $js, ['jquery'], $version, true);
            wp_register_style('bootstrap', $bootstrap, false, $version);
            wp_register_style('wpcplugin_css', $css, false, $version);
            wp_enqueue_style('bootstrap');
            wp_enqueue_style('wpcplugin_css');
        }
    }
}
