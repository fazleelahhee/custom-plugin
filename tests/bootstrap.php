<?php

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field(string $val): string
    {
        return strip_tags($val);
    }
}
