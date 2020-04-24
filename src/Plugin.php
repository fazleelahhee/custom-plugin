<?php
/**
 * Created by PhpStorm.
 * User: fazleelahee
 * Date: 23/04/2020
 * Time: 14:23
 */

namespace WPCPlugin;


class Plugin
{
    /**
     * Plugin Version
     *
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * Plugin root path
     *
     * @var string
     */
    private $basePath = '';

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Custom endpoint
     *
     * @var string
     */
    protected $endpoint = 'wpcp-plugin';

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param string $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @param $basePath
     */
    public function __construct($basePath) {
        $this->basePath = realpath(dirname($basePath));
    }


    /**
     * Init all actions and filters
     */
    public function init()
    {

//        Install::activate();
//
//        add_action('wp_loaded', [$this, 'apiRequest'], PHP_INT_MAX);
//
//        add_filter('woocommerce_email_attachments', [$this, 'attachPdfToEmail'], 99, 3);
//
//        $shortCodes = $this->shortCodes();
//        add_action('init', [$shortCodes, 'setup']);
//        add_action('vc_before_init', [$shortCodes, 'vcMaps']);
//
//        if (! is_admin()) {
//            return;
//        }
//
//        $settings = $this->settings();
//        add_action('admin_menu', [$settings, 'addMenu']);
//        add_filter(
//            'plugin_action_links_' . plugin_basename(dirname(__DIR__) . '/agb-connector.php'),
//            [$settings, 'addActionLinks']
//        );

//        add_action( 'init', [$this, 'wpse26388_rewrites_init'] );
//        add_filter( 'query_vars', [$this,'wpse26388_query_vars'] );

    }

    function wpse26388_rewrites_init(){
        add_rewrite_rule(
            $this->endpoint.'/([0-9]+)/?$',
            'index.php?pagename='.$this->endpoint.'&user_id=$matches[1]',
            'top' );
    }

    function wpse26388_query_vars( $query_vars ){
        $query_vars[] = 'user_id';
        return $query_vars;
    }


}