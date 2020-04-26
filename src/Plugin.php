<?php

namespace WPCPlugin;

use WPCPlugin\Contracts\IDataSource;

class Plugin
{
    /**
     * Plugin Version
     *
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * Plugin rewrite rule flush key
     * @var string
     */
    const PLUGIN_REWRITE_FLUSH_KEY = 'wpcpplugins_permalink_flushed';

    /**
     * Plugin root path
     *
     * @var string
     */
    private $basePath = '';

    /**
     * Data Source
     *
     * @var $dataSource iDataSource
     */
    private $dataSource;

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
    public function setDataSource(IDataSource $dataSource)
    {
        $this->dataSource = $dataSource;
        return $this;
    }

    /**
     * @param $basePath
     */
    public function __construct($basePath)
    {
        $this->basePath = realpath(dirname($basePath));
    }

    /**
     * Init all actions and filters
     */
    public function init()
    {
        Install::activate();
        $this->endpoint = WPC_PLUGIN_ENDPOINT;

        add_action('init', [$this, 'rewriteInit']);
        add_filter('query_vars', [$this, 'queryVars']);
        add_action('init', [$this, 'template']);

        add_action("wp_ajax_wpcplugin_user_collection", [$this, 'getUsers']);
        add_action("wp_ajax_nopriv_wpcplugin_user_collection", [$this, 'getUsers']);

        add_action("wp_ajax_wpcplugin_user", [$this, "getUser"]);
        add_action("wp_ajax_nopriv_wpcplugin_user", [$this, "getUser"]);

        add_action('wp_head', [$this, 'addAjaxUrl']);
        add_action('wp_enqueue_scripts', [$this, 'wpcpEnqueueScripts']);

        return $this;
    }

    /**
     * Add rewrite rule to WP rewrites to allow view custom endpoint
     */
    public function rewriteInit()
    {
        add_rewrite_rule($this->endpoint . '/([0-9]+)/?$', 'index.php?pagename=' . $this->endpoint . '&user_id=$matches[1]', 'top');
        if (!get_option(self::PLUGIN_REWRITE_FLUSH_KEY)) {
            flush_rewrite_rules(false);
            update_option(self::PLUGIN_REWRITE_FLUSH_KEY, 1);
        }
    }

    /**
     * Accept user id as a query var from url
     * @param $queryVars
     * @return array
     */
    public function queryVars($queryVars)
    {
        $queryVars[] = 'user_id';
        return $queryVars;
    }

    /**
     * Add template to the custom end point. Initially this function will look for template in the current active themes directory,
     * if not exists then it will use default template template from plugin.
     */
    public function template()
    {
        $urlPath = trim(parse_url(add_query_arg([]), PHP_URL_PATH), '/');
        if (preg_match('/' . $this->endpoint . '\/?([0-9]+)?$/', $urlPath)) {
            // load the file if exists
            $load = locate_template('template-' . $this->endpoint . '.php', true);
            if (!$load) {
                //load default template
                include_once "{$this->basePath}/template-default.php";
            }
            exit(); // just exit if template was found and loaded
        }
    }

    public function getUsers()
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
            $output['data'] = $this->recursiveSanitizeTextField($user->getUsers());
            $output['status'] = 'success';
        } catch (\Exception $exp) {
            $output['data'] = $exp->getMessage();
            $output['status'] = 'error';
            wp_send_json_error($output);
        }
        $output = apply_filters('wpcp_plugin_user_collection', $output);
        wp_send_json($output);
    }

    public function getUser()
    {
        $output = [];
        try {
            $user = new User($this->dataSource, WPC_PLUGIN_API_ENDPOINT);
            $userId = (int)isset($_REQUEST['user_id']) ? sanitize_text_field(wp_unslash($_REQUEST['user_id'])) : 0;

            if (empty($userId)) {
                throw new \Exception('User id is empty');
            }
            $output['data'] = $this->recursiveSanitizeTextField($user->getUserById($userId));
            $output['status'] = 'success';
        } catch (\Exception $exp) {
            $output['data'] = $exp->getMessage();
            $output['status'] = 'error';
            wp_send_json_error($output);
        }
        $output = apply_filters('wpcp_plugin_user_by_id', $output);
        wp_send_json($output);
    }

    public function addAjaxUrl()
    {
        $urlPath = trim(parse_url(add_query_arg([]), PHP_URL_PATH), '/');
        if (preg_match('/' . $this->endpoint . '\/?([0-9]+)?$/', $urlPath)) {
            ?>
            <script type="text/javascript">
                var ajaxurl = <?php echo json_encode(admin_url("admin-ajax.php")); ?>;
                var ajaxnonce = <?php echo json_encode(wp_create_nonce("itr_ajax_nonce")); ?>;
            </script>
            <?php
        }
    }

    public function wpcpEnqueueScripts($hook)
    {
        $urlPath = trim(parse_url(add_query_arg([]), PHP_URL_PATH), '/');
        if (preg_match('/' . $this->endpoint . '\/?([0-9]+)?$/', $urlPath)) {
            $js = WPC_PLUGIN_URL . 'public/js/wpcplugin.js';
            $css = WPC_PLUGIN_URL . 'public/css/wpcplugin.css';
            $bootstrap = WPC_PLUGIN_URL . 'public/css/bootstrap.min.css';
            $version = WP_DEBUG ?  time() :  self::VERSION;
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
    public function recursiveSanitizeTextField($array) {
        foreach ( $array as $key => &$value ) {
            if ( is_array( $value ) ) {
                $value = $this->recursiveSanitizeTextField($value);
            }
            else {
                $value = sanitize_text_field( $value );
            }
        }

        return $array;
    }
}
