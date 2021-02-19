<?php

if (! function_exists('stubkit')) {
    /**
     * The StubKit expression helper.
     * @param string $expression
     * @param array $variables
     * @return string
     */
    function stubkit(string $expression, array $variables = [])
    {
        return \StubKit\Facades\StubKit::helper($expression, $variables);
    }
}
