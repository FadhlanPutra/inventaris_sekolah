<?php

// config for JibayMcs/FilamentTour
return [

    'only_visible_once' => true,
    'enable_css_selector' => false,

    'enabled' => (bool) env('ENABLE_TOUR', true),

    'tour_prefix_id' => 'tour_',
    'highlight_prefix_id' => 'highlight_',
];
