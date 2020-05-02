<?php

namespace WPCPlugin\Hooks;

class AbstractHook
{
    public function init(): array
    {
        return [];
    }

    public function jsonDispatch(): void
    {
        try {
            wp_send_json($this->init());
        } catch (\Exception $exp) {
            $output['data'] = $exp->getMessage();
            wp_send_json_error($output, 422);
        }
    }

    public function dispatch(): void
    {
        try {
            $this->init();
            exit();
        } catch (\Exception $exp) {
            status_header($exp->getCode());
            nocache_headers();
            include(get_query_template($exp->getCode()));
            exit();
        }
    }

    public function attach(): void
    {
        $this->init();
    }
}
