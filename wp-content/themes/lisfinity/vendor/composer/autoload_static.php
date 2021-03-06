<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3cd55e33b2bb886ccf946b5a4bef9317
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Whoops\\' => 7,
        ),
        'S' => 
        array (
            'Symfony\\Component\\Debug\\' => 24,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'L' => 
        array (
            'Lisfinity\\' => 10,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Whoops\\' => 
        array (
            0 => __DIR__ . '/..' . '/filp/whoops/src/Whoops',
        ),
        'Symfony\\Component\\Debug\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/debug',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Lisfinity\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/src',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3cd55e33b2bb886ccf946b5a4bef9317::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3cd55e33b2bb886ccf946b5a4bef9317::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
