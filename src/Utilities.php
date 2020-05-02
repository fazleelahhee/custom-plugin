<?php

namespace WPCPlugin;

class Utilities
{
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
