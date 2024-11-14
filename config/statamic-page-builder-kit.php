<?php

use Justbetter\StatamicPageBuilderKit\Fieldsets\Components\ComponentTextFieldset;
use Justbetter\StatamicPageBuilderKit\Fieldsets\Components\ComponentUspsFieldset;
use Justbetter\StatamicPageBuilderKit\Fieldsets\Components\ComponentImageTextFieldset;
use Justbetter\StatamicPageBuilderKit\Fieldsets\Components\ComponentHeroBannerFieldset;

return [
    'groups' => [
        'general' => [
            'title' => __('General'),
            'sets' => []
        ],
        'banners' => [
            'title' => __('Banners'),
            'sets' => []
        ],
    ],

    'sets' => [
        'general' => [
            ComponentTextFieldset::class,
            ComponentUspsFieldset::class,
            ComponentImageTextFieldset::class,
        ],
        'banners' => [
            ComponentHeroBannerFieldset::class
        ]
    ],
    'additional_sets' => [],
];