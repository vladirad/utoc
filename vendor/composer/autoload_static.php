<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf24b5159dbebfaafc3ae640d5135f9c5
{
    public static $files = array(
        '16eed290c5592c18dc3f16802ad3d0e4' => __DIR__ . '/..' . '/ivopetkov/html5-dom-document-php/autoload.php',
    );

    public static $prefixLengthsPsr4 = array(
        'D' =>
        array(
            'Devstetic\\Utoc\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array(
        'Devstetic\\Utoc\\' =>
        array(
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array(
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf24b5159dbebfaafc3ae640d5135f9c5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf24b5159dbebfaafc3ae640d5135f9c5::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf24b5159dbebfaafc3ae640d5135f9c5::$classMap;
        }, null, ClassLoader::class);
    }
}
