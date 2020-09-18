<?php

namespace App;

use Illuminate\Support\Facades\File;

class Stubs
{
    public static function makeDirectory(string $directory, Flags $flags)
    {
        if (File::exists($flags->packageDirectory().'/'.$directory)) {
            return;
        }

        File::makeDirectory($flags->packageDirectory().'/'.$directory);
    }

    public static function copyDirectory(string $source, string $destination, Flags $flags)
    {
        File::copyDirectory(STUBS_DIRECTORY.'/'.$source, $flags->packageDirectory().'/'.$destination);
    }

    public static function copy(string $source, string $destination, Flags $flags)
    {
        File::copy(STUBS_DIRECTORY.'/'.$source, $flags->packageDirectory().'/'.$destination);
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
        $serviceProvider = str_replace("#{$name}#", File::get(STUBS_DIRECTORY.'/'.$stubSource), $serviceProvider);

        File::put($flags->packageDirectory().'/src/DummyPackageServiceProvider.php', $serviceProvider);
    }
}
