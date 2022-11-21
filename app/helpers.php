<?php

function set_active($path, $active = 'active') {
    $path = ltrim($path, '/');
    return Request::is($path) ? $active : '';
}