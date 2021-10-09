<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    |
    | The locales are allowed to interact with your application.
    |
    */
    'locales' => [
        'vi',
        'en'
    ],

    /*
    |--------------------------------------------------------------------------
    | The name of locale column's traslation tables
    |--------------------------------------------------------------------------
    |
    | It must be matched with the name of locale column's translation tables in your application.
    | You can also configure it on each translation model.
    |
    */
    'locale_key' => 'locale',

    /*
    |--------------------------------------------------------------------------
    | The namespace of translation models
    |--------------------------------------------------------------------------
    |
    | It be used to assign default namespace for translation models.
    | You can also configure it on each translation model.
    */
    'translation_namespace' => 'App\Models\Translation',
];
