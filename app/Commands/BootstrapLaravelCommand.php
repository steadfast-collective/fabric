<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class BootstrapLaravelCommand extends Command
{
    protected $signature = 'laravel {name} {--tests} {--facade} {--config} {--views} {--lang} {--routes}';
    protected $description = 'Bootstrap a Laravel package.';

    protected $flags;
    protected $vendorName;
    protected $packageName;
    protected $packageDirectory;
    protected $packageNamespace;

    public function handle()
    {
        $this->flags = [
            'name'   => $this->argument('name'),
            'params' => [
                'tests'  => $this->option('tests'),
                'facade' => $this->option('facade'),
                'config' => $this->option('config'),
                'views'  => $this->option('views'),
                'lang'   => $this->option('lang'),
                'routes' => $this->option('routes'),
            ],
        ];

        $this->vendorName = explode('/', $this->flags['name'])[0];
        $this->packageName = explode('/', $this->flags['name'])[1];
        $this->packageDirectory = getcwd().'/'.Str::slug($this->packageName);
        $this->packageNamespace = Str::studly($this->vendorName).'\\'.Str::studly($this->packageName);

        // TODO: wizard (if no params are set)

        $this->task('Copying stubs', function () {
            File::copyDirectory(STUBS_DIRECTORY.'/laravel', $this->packageDirectory);
        });

        if (in_array('tests', $this->flags['params']) && $this->flags['params']['tests'] === true) {
            $this->task('Adding Tests', function () {
                File::copyDirectory(STUBS_DIRECTORY.'/laravel-tests/tests', $this->packageDirectory.'/tests');
                File::copy(STUBS_DIRECTORY.'/laravel-tests/phpunit.xml', $this->packageDirectory.'/phpunit.xml');

                $composerManifest = json_decode(File::get($this->packageDirectory.'/composer.json'), true);
                $composerManifest['autoload-dev']['psr-4'][Str::studly($this->vendorName)."\\".Str::studly($this->packageName)."\\Tests\\"] = "tests";
                $composerManifest['require-dev']['phpunit/phpunit'] = "^9.3";

                File::put($this->packageDirectory.'/composer.json', json_encode($composerManifest, JSON_FORCE_OBJECT|JSON_PRETTY_PRINT));
            });
        }

        $this->task('Swapping namespaces, classes, etc', function () {
            collect(File::allFiles($this->packageDirectory))
                ->each(function (SplFileInfo $file) {
                    $contents = $file->getContents();

                    // Vendor
                    $contents = str_replace('DummyVendor', Str::studly($this->vendorName), $contents);
                    $contents = str_replace('dummy-vendor', $this->vendorName, $contents);

                    // Package Name
                    $contents = str_replace('DummyPackage', Str::studly($this->packageName), $contents);
                    $contents = str_replace('dummy-package', $this->packageName, $contents);

                    // Classes
                    $contents = str_replace('DummyClass', Str::studly($this->packageName), $contents);

                    if ($file->getFilename() === 'DummyClass.php') {
                        File::put($file->getPath().'/'.Str::studly($this->packageName).'.php', $contents);
                        File::delete($file->getPathname());

                        return;
                    }

                    File::put($file->getPathname(), $contents);
                });
        });

        $this->task('Composer Install', function () {
            $this->line('');

            $process = new Process(['composer install'], $this->packageDirectory);
            $process->run();
        });
    }
}
