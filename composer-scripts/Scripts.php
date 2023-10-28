<?php

namespace Velsym\ComposerScripts;

use Composer\InstalledVersions;
use Composer\Package\CompletePackage;
use Composer\Installer\PackageEvent;

class Scripts
{
    public static function postPackageInstall(PackageEvent $event): void
    {
        $path = self::getPackagePath($event) . "/velsym-dependencies";
        if ($isVelsymCompatible = is_dir($path)) {
            $dependenciesPath = $path . "/dependencies.php";
            self::addDependency($dependenciesPath);
        }
    }

    public static function prePackageUninstall(PackageEvent $event)
    {
        $path = self::getPackagePath($event) . "/velsym-dependencies";
        if ($isVelsymCompatible = is_dir($path)) {
            $dependenciesPath = $path . "/dependencies.php";
            self::removeDependency($dependenciesPath);
        }
    }

    private static function addDependency(string $dependencyPath): void
    {
        $relativeDependencyPath = self::getRelativePath($dependencyPath);
        $mainDependenciesPath = self::getMainDependenciesPath();
        $mainDependenciesContent = file($mainDependenciesPath);
        $newLineDependency = ["    require('../'.'$relativeDependencyPath'),\n"];
        array_splice($mainDependenciesContent, count($newLineDependency) - 2, 0, $newLineDependency);
        file_put_contents($mainDependenciesPath, $mainDependenciesContent);
    }

    private static function removeDependency(string $dependencyPath): void
    {
        $relativeDependencyPath = self::getRelativePath($dependencyPath);
        $mainDependenciesPath = self::getMainDependenciesPath();
        $mainDependenciesContent = file($mainDependenciesPath);
        $dependencyLineIndex = self::array_search_partial($mainDependenciesContent, $relativeDependencyPath);
        if ($dependencyLineIndex === -1) return;
        unset($mainDependenciesContent[$dependencyLineIndex]);
        file_put_contents($mainDependenciesPath, $mainDependenciesContent);
        self::removeDependency($dependencyPath); // In case of duplicate require
    }

    private static function getPackagePath(PackageEvent $event): string
    {
        /** @var CompletePackage $package */
        $package = $event->getOperation()->getPackage();

        return realpath(InstalledVersions::getInstallPath($package->getName()));
    }

    private static function getRootPath(): false|string
    {
        return realpath(__DIR__ . "/..");
    }

    private static function getRelativePath(string $finalPath): array|string
    {
        $rootPath = self::getRootPath();
        $relative = str_replace($rootPath . DIRECTORY_SEPARATOR, "", $finalPath);
        return str_replace("\\", "/", $relative);
    }

    private static function getMainDependenciesPath(): false|string
    {
        return realpath(self::getRootPath() . "/src/config/dependencies.php");
    }

    private static function array_search_partial($arr, $keyword): int|string
    {
        foreach ($arr as $index => $string) {
            if (strpos($string, $keyword) !== FALSE)
                return $index;
        }
        return -1;
    }

    public static function dbLoadModels(): void
    {
        require self::getRootPath() . "/src/config/database-models.php";
    }

    public static function serverStart(): void
    {
        $rootDir = self::getRootPath();
        system("cd $rootDir/src && php -S 127.0.0.1:1234 index.php");
    }
}