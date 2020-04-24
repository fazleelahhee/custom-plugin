<?php
/**
 * Created by PhpStorm.
 * User: fazleelahee
 * Date: 23/04/2020
 * Time: 16:03
 */

namespace WPCPlugin;


class Template
{
    private $plugin;
    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
    }

    public function load() {
        $url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');
        if ( preg_match('/'.$this->plugin->getEndpoint().'\/?([0-9]+)?$/', $url_path ) ) {
            // load the file if exists
            $load = locate_template('template-'.$this->plugin->getEndpoint().'.php', true);
            if (!$load) {
                //load default template
                include_once $this->plugin->getBasePath()."/template-default.php";
            }

            exit(); // just exit if template was found and loaded
        }
    }
}