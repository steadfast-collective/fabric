<?php

namespace SteadfastCollective\Fabric;

use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class Stubs
{
    public static function downloadStubs(Flags $flags)
    {
        if (File::exists($flags->clonedPath())) {
            self::deleteStubs($flags);
        }

        $cloneProcess = new Process(['git', 'clone', 'git@github.com:steadfast-collective/fabric.git', $flags->clonedPath()]);
        $cloneProcess->run();
    }

    public static function deleteStubs(Flags $flags)
    {
        File::deleteDirectory($flags->clonedPath());
    }

    public static function makeDirectory(string $directory, Flags $flags)
    {
        if (File::exists($flags->packageDirectory().'/'.$directory)) {
            return;
        }

        File::makeDirectory($flags->packageDirectory().'/'.$directory);
    }

    public static function copyDirectory(string $source, string $destination, Flags $flags)
    {
        File::copyDirectory($flags->clonedStubsPath().'/'.$source, $flags->packageDirectory().'/'.$destination);
    }

    public static function copy(string $source, string $destination, Flags $flags)
    {
        File::copy($flags->clonedStubsPath().'/'.$source, $flags->packageDirectory().'/'.$destination);
    }

    public static function mergeManifest(array $manifest, Flags $flags)
    {
        $composerManifest = json_decode(File::get($flags->packageDirectory().'/composer.json'), true);
        $composerManifest = array_merge($manifest, $composerManifest);

        File::put($flags->packageDirectory().'/composer.json', json_encode($composerManifest, JSON_PRETTY_PRINT));
    }

    public static function fillServiceProviderStub(string $name, string $stubSource, Flags $flags)
    {
        $serviceProvider = File::get($flags->packageDirectory().'/src/DummyPackageServiceProvider.php');
        $serviceProvider = str_replace("#{$name}#", File::get($flags->clonedStubsPath().'/'.$stubSource), $serviceProvider);

        File::put($flags->packageDirectory().'/src/DummyPackageServiceProvider.php', $serviceProvider);
    }
}
