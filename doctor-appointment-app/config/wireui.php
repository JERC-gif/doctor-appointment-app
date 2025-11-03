<?php

use WireUi\Components;
use WireUi\Enum\Packs;
use WireUi\WireUiConfig as Config;

return [

    /*
    |--------------------------------------------------------------------------
    | Prefix
    |--------------------------------------------------------------------------
    | Deja null para usar <x-button>. Usaremos alias para soportar <x-wire-button>.
    */
    'prefix' => null,

    /*
    |--------------------------------------------------------------------------
    | Global Styles
    |--------------------------------------------------------------------------
    */
    'style' => [
        'shadow'  => Packs\Shadow::BASE,
        'rounded' => Packs\Rounded::MD,
        'color'   => Packs\Color::PRIMARY,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Configuration
    |--------------------------------------------------------------------------
    */
    'alert'           => Config::alert(),
    'avatar'          => Config::avatar(),
    'badge'           => Config::badge(),
    'mini-badge'      => Config::miniBadge(),
    'button'          => Config::button(),
    'mini-button'     => Config::miniButton(),
    'card'            => Config::card(),
    'checkbox'        => Config::checkbox(),
    'color-picker'    => Config::wrapper(),
    'datetime-picker' => Config::dateTimePicker(),
    'dialog'          => Config::dialog(),
    'dropdown'        => Config::dropdown(),
    'icon'            => Config::icon(),
    'input'           => Config::wrapper(),
    'currency'        => Config::wrapper(),
    'maskable'        => Config::wrapper(),
    'number'          => Config::wrapper(),
    'password'        => Config::wrapper(),
    'phone'           => Config::wrapper(),
    'link'            => Config::link(),
    'modal'           => Config::modal(),
    'modal-card'      => Config::modal(),
    'native-select'   => Config::wrapper(),
    'notifications'   => Config::notifications(),
    'radio'           => Config::radio(),
    'select'          => Config::wrapper(),
    'textarea'        => Config::wrapper(),
    'time-picker'     => Config::timePicker(),
    'time-selector'   => Config::timeSelector(),
    'toggle'          => Config::toggle(),

    /*
    |--------------------------------------------------------------------------
    | WireUI Components (aliases)
    |--------------------------------------------------------------------------
    | Creamos alias para que <x-wire-button> funcione.
    */
    'components' => Config::defaultComponents([
        'button' => [
            'alias' => 'wire-button', // <x-wire-button> ahora apunta al botón de WireUI
        ],
        // Puedes agregar más alias similares si usas otros <x-wire-...>
    ]),
];
