<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit110f1837a030683c7a1fb34816912281
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'AmoCRM\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'AmoCRM\\' => 
        array (
            0 => __DIR__ . '/..' . '/andrey-tech/amocrm-api-php/src/AmoCRM',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit110f1837a030683c7a1fb34816912281::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit110f1837a030683c7a1fb34816912281::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit110f1837a030683c7a1fb34816912281::$classMap;

        }, null, ClassLoader::class);
    }
}