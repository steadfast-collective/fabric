<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class BootstrapLaravelCommand extends Command
{
    protected $signature = 'laravel {name} {--tests} {--facade} {--config} {--views} {--lang} {--routes} {--migrations}';
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
                'migrations' => $this->option('migrations'),
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

        if ($this->flags['params']['tests'] === true) {
            $this->task('Adding Tests', function () {
                File::copyDirectory(STUBS_DIRECTORY.'/laravel-tests/tests', $this->packageDirectory.'/tests');
                File::copy(STUBS_DIRECTORY.'/laravel-tests/phpunit.xml', $this->packageDirectory.'/phpunit.xml');

                $composerManifest = json_decode(File::get($this->packageDirectory.'/composer.json'), true);
                $composerManifest['autoload-dev']['psr-4'][Str::studly($this->vendorName)."\\".Str::studly($this->packageName)."\\Tests\\"] = "tests";
                $composerManifest['require-dev']['orchestra/testbench'] = '^4.0|^5.0|^6.0';
                $composerManifest['require-dev']['phpunit/phpunit'] = "^8.0|^9.0";

                File::put($this->packageDirectory.'/composer.json', json_encode($composerManifest, JSON_PRETTY_PRINT));
            });
        }

        if ($this->flags['params']['facade'] === true) {
            $this->task('Adding Facade', function () {
                File::copy(STUBS_DIRECTORY.'/DummyPackageFacade.php', $this->packageDirectory.'/src/DummyPackageFacade.php');

                $composerManifest = json_decode(File::get($this->packageDirectory.'/composer.json'), true);
                $composerManifest['extra']['laravel']['aliases'][Str::studly($this->packageName)] = 'DummyVendor\\DummyPackage\\DummyPackageFacade';

                File::put($this->packageDirectory.'/composer.json', json_encode($composerManifest, JSON_PRETTY_PRINT));
            });
        }

        if ($this->flags['params']['config'] === true) {
            $this->task('Adding Config', function () {
                File::makeDirectory($this->packageDirectory.'/config');
                File::copy(STUBS_DIRECTORY.'/laravel-config-dummy.php', $this->packageDirectory.'/config/dummy-package.php');

                $serviceProvider = File::get($this->packageDirectory.'/src/DummyPackageServiceProvider.php');
                $serviceProvider = str_replace('#CONFIG#', File::get(STUBS_DIRECTORY.'/laravel-boot-config.php'), $serviceProvider);

                File::put($this->packageDirectory.'/src/DummyPackageServiceProvider.php', $serviceProvider);
            });
        }

        if ($this->flags['params']['views'] === true) {
            $this->task('Adding Views', function () {
                File::makeDirectory($this->packageDirectory.'/resources');
                File::makeDirectory($this->packageDirectory.'/resources/views');
                File::copy(STUBS_DIRECTORY.'/.gitkeep', $this->packageDirectory.'/resources/views/.gitkeep');

                $serviceProvider = File::get($this->packageDirectory.'/src/DummyPackageServiceProvider.php');
                $serviceProvider = str_replace('#VIEWS#', File::get(STUBS_DIRECTORY.'/laravel-boot-views.php'), $serviceProvider);

                File::put($this->packageDirectory.'/src/DummyPackageServiceProvider.php', $serviceProvider);
            });
        }

        if ($this->flags['params']['lang'] === true) {
            $this->task('Adding Language Files', function () {
                File::makeDirectory($this->packageDirectory.'/resources');
                File::makeDirectory($this->packageDirectory.'/resources/lang');
                File::copy(STUBS_DIRECTORY.'/.gitkeep', $this->packageDirectory.'/resources/lang/.gitkeep');

                $serviceProvider = File::get($this->packageDirectory.'/src/DummyPackageServiceProvider.php');
                $serviceProvider = str_replace('#LANG#', File::get(STUBS_DIRECTORY.'/laravel-boot-lang.php'), $serviceProvider);

                File::put($this->packageDirectory.'/src/DummyPackageServiceProvider.php', $serviceProvider);
            });
        }

        if ($this->flags['params']['routes'] === true) {
            $this->task('Adding Routes', function () {
                File::makeDirectory($this->packageDirectory.'/routes');
                File::copy(STUBS_DIRECTORY.'/laravel-routes-web.php', $this->packageDirectory.'/routes/web.php');

                $serviceProvider = File::get($this->packageDirectory.'/src/DummyPackageServiceProvider.php');
                $serviceProvider = str_replace('#ROUTES#', File::get(STUBS_DIRECTORY.'/laravel-boot-routes.php'), $serviceProvider);

                File::put($this->packageDirectory.'/src/DummyPackageServiceProvider.php', $serviceProvider);
            });
        }

        if ($this->flags['params']['migrations'] === true) {
            $this->task('Adding Migrations', function () {
                File::makeDirectory($this->packageDirectory.'/database');
                File::makeDirectory($this->packageDirectory.'/database/migrations');
                File::copy(STUBS_DIRECTORY.'/.gitkeep', $this->packageDirectory.'/database/migrations/.gitkeep');

                $serviceProvider = File::get($this->packageDirectory.'/src/DummyPackageServiceProvider.php');
                $serviceProvider = str_replace('#MIGRATIONS#', File::get(STUBS_DIRECTORY.'/laravel-boot-migrations.php'), $serviceProvider);

                File::put($this->packageDirectory.'/src/DummyPackageServiceProvider.php', $serviceProvider);
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
                    $contents = str_replace('DummyPackageServiceProvider', Str::studly($this->packageName).'ServiceProvider', $contents);
                    $contents = str_replace('DummyPackageFacade', Str::studly($this->packageName).'Facade', $contents);

                    if ($file->getFilename() === 'DummyPackageServiceProvider.php') {
                        // TODO: remove left over temp comments, like '#MIGRATIONS#'

                        File::put($file->getPath().'/'.Str::studly($this->packageName).'ServiceProvider.php', $contents);
                        File::delete($file->getPathname());

                        return;
                    }

                    if ($file->getFilename() === 'DummyPackageFacade.php') {
                        File::put($file->getPath().'/'.Str::studly($this->packageName).'Facade.php', $contents);
                        File::delete($file->getPathname());

                        return;
                    }

                    File::put($file->getPathname(), $contents);
                });

            // TODO: Run php cs fixer to lint the service provider etc
        });

        $this->task('Composer Install', function () {
            $this->line('');

            $process = new Process(['composer install'], $this->packageDirectory);
            $process->run();
        });
    }
}
