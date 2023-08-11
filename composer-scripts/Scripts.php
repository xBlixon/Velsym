<?php

namespace Velsym\ComposerScripts;

use Composer\InstalledVersions;
use Composer\Package\CompletePackage;
use Composer\Installer\PackageEvent;

class Scripts
{
    public static function postPackageInstall(PackageEvent $event): void
    {
        /** @var CompletePackage $package */
        $package = $event->getOperation()->getPackage();

        $path = realpath(InstalledVersions::getInstallPath($package->getName()));
        $path = $path . DIRECTORY_SEPARATOR . "velsym-dependencies";
        $isVelsymCompatible = is_dir($path);
        if ($isVelsymCompatible) {
            $dependenciesPath = $path . DIRECTORY_SEPARATOR . "dependencies.php";
            self::addDependency($dependenciesPath);
        }
    }

    public static function addDependency(string $dependencyPath): void
    {
        $slash = DIRECTORY_SEPARATOR;
        $projectRoot = realpath(__DIR__ . "$slash..");
        $relativeDependencyPath = str_replace($projectRoot . $slash, "", $dependencyPath);
        $mainDependenciesPath = realpath("$projectRoot{$slash}src{$slash}dependencies.php");
        $mainDependenciesContent = file($mainDependenciesPath);
        $newLineDependency = ["    require('$relativeDependencyPath'),\n"];
        array_splice($mainDependenciesContent, count($newLineDependency) - 2, 0, $newLineDependency);
        file_put_contents($mainDependenciesPath, $mainDependenciesContent);
    }
}