<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit00e356fc3e72a1e51c991c9caeb24d0f
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('WPUM\Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \WPUM\Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit00e356fc3e72a1e51c991c9caeb24d0f', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \WPUM\Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit00e356fc3e72a1e51c991c9caeb24d0f', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\WPUM\Composer\Autoload\ComposerStaticInit00e356fc3e72a1e51c991c9caeb24d0f::getInitializer($loader));

        $loader->register(true);

        $filesToLoad = \WPUM\Composer\Autoload\ComposerStaticInit00e356fc3e72a1e51c991c9caeb24d0f::$files;
        $requireFile = \Closure::bind(static function ($fileIdentifier, $file) {
            if (empty($GLOBALS['__composer_autoload_files'][$fileIdentifier])) {
                $GLOBALS['__composer_autoload_files'][$fileIdentifier] = true;

                require $file;
            }
        }, null, null);
        foreach ($filesToLoad as $fileIdentifier => $file) {
            $requireFile($fileIdentifier, $file);
        }

        return $loader;
    }
}
