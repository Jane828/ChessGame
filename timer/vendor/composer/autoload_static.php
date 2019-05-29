<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit636a5a174224f36c8602b28eb1dead56
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Workerman\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Workerman\\' => 
        array (
            0 => __DIR__ . '/..' . '/walkor/workerman',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit636a5a174224f36c8602b28eb1dead56::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit636a5a174224f36c8602b28eb1dead56::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}