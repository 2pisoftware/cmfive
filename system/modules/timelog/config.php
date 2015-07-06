<?php

Config::set('timelog', [
    'active'    => true,
    'path'      => 'system/modules',
    'topmenu'   => false,         // Top menu false as widget is injected via core_template hook
    'hooks'     => ['core_template']
]);