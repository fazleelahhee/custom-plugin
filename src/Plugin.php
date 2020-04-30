<?php

declare(strict_types=1);

namespace WPCPlugin;

use WPCPlugin\Admin\Settings;
use WPCPlugin\Contracts\IDataSource;

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
     * Data Source
     *
     * @var $dataSource iDataSource
     */
    protected $dataSource;

    /**
     * Custom endpoint
     *
     * @var string
     */
    protected $endpoint = '';

    /**
     * @param iDataSource $dataSource
     * @return $this
     */
    public function addDataSource(IDataSource $dataSource): Plugin
    {
        $this->dataSource = $dataSource;
        return $this;
    }

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

        add_action("wp_ajax_wpcplugin_user_collection", [$this, 'userCollection']);
        add_action("wp_ajax_nopriv_wpcplugin_user_collection", [$this, 'userCollection']);

        add_action("wp_ajax_wpcplugin_user", [$this, "user"]);
        add_action("wp_ajax_nopriv_wpcplugin_user", [$this, "user"]);

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

    public function userCollection(): void
    {
        $output = [
            'field_display' => [
                ['key' => 'id', 'label' => 'ID', 'link' => 'y'],
                ['key' => 'name', 'label' => 'Name', 'link' => 'y'],
                ['key' => 'username', 'label' => 'Username', 'link' => 'y'],
                ['key' => 'email', 'label' => 'Email', 'link' => 'n'],
                ['key' => 'phone', 'label' => 'Telephone', 'link' => 'n'],
            ],
        ];
        try {
            $user = new User($this->dataSource, WPC_PLUGIN_API_ENDPOINT);
            /**
             * Recursivelly sanitised all data come from external api.
             * because its not in our control and we don't know what type of data coming through
             *so its must be sanitised.
             */
            $output['data'] = $this->recursiveSanitizeField($user->allUser());
        } catch (\Exception $exp) {
            $output['data'] = $exp->getMessage();
            wp_send_json_error($output, 422);
        }
        /**
         * Allow other plugins/ theme developer to modify ajax response.
         */
        $output = apply_filters('wpcp_plugin_user_collection', $output);
        wp_send_json($output);
    }

    public function user(): void
    {
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        $output = [
            'field_display' => [
                0 => ['key' => 'id', 'label' => 'ID'],
                1 => ['key' => 'name', 'label' => 'Name'],
                2 => ['key' => 'username', 'label' => 'Username'],
                3 => ['key' => 'email', 'label' => 'Email'],
                4 => ['key' => 'phone', 'label' => 'Telephone'],
                5 => ['key' => 'website', 'label' => 'Website'],
                6 => ['key' => 'address', 'label' => 'Address'],
                7 => ['key' => 'company', 'label' => 'Company'],
            ],
        ];
        try {
            $user = new User($this->dataSource, WPC_PLUGIN_API_ENDPOINT);
            $userId = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;

            if (empty($userId)) {
                throw new \Exception('User id is empty');
            }
            /**
             * Recursivelly sanitised all data come from external api.
             * because its not in our control and we don't know what type of data coming through
             *so its must be sanitised.
             */
            $response = $this->recursiveSanitizeField($user->findUserById($userId));
            foreach ($response as $key => $value) {
                $output['data'][$key] = $value;
                if (in_array($key, ['address', 'company'], true)) {
                    $value = \array_diff_key($value, ['geo' => "xy",
                        'catchPhrase' => "xy",
                        'bs' => "xy", ]);
                    $output['data'][$key] = implode('<br>', $value);
                }
            }
        } catch (\Exception $exp) {
            $output['data'] = $exp->getMessage();
            wp_send_json_error($output, 422);
        }
        /**
         * Allow other plugins/ theme developer to modify ajax response.
         */
        $output = apply_filters('wpcp_plugin_single_user', $output);
        wp_send_json($output);
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

    /**
     * Recursive sanitation for an array
     *
     * @param $array
     *
     * @return mixed
     */
    public function recursiveSanitizeField(array $array): array
    {
        foreach ($array as $key => &$value) {
            $value = is_array($value) ? $this->recursiveSanitizeField($value) : sanitize_text_field($value);
        }
        return $array;
    }
}
