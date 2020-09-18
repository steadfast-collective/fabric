<?php

namespace SteadfastCollective\Fabric\Commands;

use SteadfastCollective\Fabric\Flags;
use SteadfastCollective\Fabric\Stubs;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Finder\SplFileInfo;

class LaravelCommand extends Command
{
    protected $signature = 'laravel {name} {--tests} {--facade} {--config} {--views} {--lang} {--routes} {--migrations}';
    protected $description = 'Bootstrap a Laravel package.';

    public function handle()
    {
        $flags = new Flags($this->argument('name'), [
            'tests'      => $this->option('tests'),
            'facade'     => $this->option('facade'),
            'config'     => $this->option('config'),
            'views'      => $this->option('views'),
            'lang'       => $this->option('lang'),
            'routes'     => $this->option('routes'),
            'migrations' => $this->option('migrations'),
        ]);

        if ($flags->hasEmptyParams()) {
            $params['tests'] = $this->confirm('Should we setup PHPUnit for you?');
            $params['facade'] = $this->confirm('Would you like a facade?');
            $params['config'] = $this->confirm('Would you like a configuration file?');
            $params['views'] = $this->confirm('Would you like your views to be setup?');
            $params['lang'] = $this->confirm('Would you like language files to be setup?');
            $params['routes'] = $this->confirm('Would you like routes to be setup?');
            $params['migrations'] = $this->confirm('Would you like your migrations to be setup?');

            $flags->params($params);
        }

        $this->task('Copying stubs', function () use ($flags) {
            Stubs::copyDirectory('laravel', '.', $flags);
        });

        // Tests
        $this->task('Tests', function () use ($flags) {
            if (! $flags->hasParam('tests')) {
                return;
            }

            Stubs::copyDirectory('laravel-tests/tests', 'tests', $flags);
            Stubs::copy('laravel-tests/phpunit.xml', 'phpunit.xml', $flags);
            Stubs::mergeManifest([
                'autoload-dev' => [
                    'psr-4' => [
                        Str::studly($flags->vendorName())."\\".Str::studly($flags->packageName())."\\Tests\\" => "tests",
                    ],
                ],
                'require-dev' => [
                    'orchestra/testbench' => '^4.0|^5.0|^6.0',
                    'phpunit/phpunit' => '^8.0|^9.0',
                ],
            ], $flags);
        });

        // Facade
        $this->task('Facade', function () use ($flags) {
        if (! $flags->hasParam('facade')) {
                return;
            }

            Stubs::copy('DummyPackageFacade.php', 'src/DummyPackageFacade.php', $flags);
            Stubs::mergeManifest([
                'extra' => [
                    'laravel' => [
                        'aliases' => [
                            Str::studly($flags->packageName()) => 'DummyVendor\\DummyPackage\\DummyPackageFacade',
                        ],
                    ],
                ],
            ], $flags);
        });

        // Config
        $this->task('Config', function () use ($flags) {
            if (! $flags->hasParam('config')) {
                return;
            }

            Stubs::makeDirectory('config', $flags);
            Stubs::copy('laravel-config-dummy.php', 'config/dummy-package.php', $flags);
            Stubs::fillServiceProviderStub('CONFIG', 'laravel-boot-config.php', $flags);
        });

        // Views
        $this->task('Views', function () use ($flags) {
            if (! $flags->hasParam('views')) {
                return;
            }

            Stubs::makeDirectory('resources', $flags);
            Stubs::makeDirectory('resources/views', $flags);
            Stubs::copy('.gitkeep', 'resources/views/.gitkeep', $flags);
            Stubs::fillServiceProviderStub('VIEWS', 'laravel-boot-views.php', $flags);
        });

        // Language Files
        $this->task('Language Files', function () use ($flags) {
            if (! $flags->hasParam('lang')) {
                return;
            }

            Stubs::makeDirectory('resources', $flags);
            Stubs::makeDirectory('resources/lang', $flags);
            Stubs::copy('.gitkeep', 'resources/lang/.gitkeep', $flags);
            Stubs::fillServiceProviderStub('LANG', 'laravel-boot-lang.php', $flags);
        });

        // Routes
        $this->task('Routes', function () use ($flags) {
            if (! $flags->hasParam('routes')) {
                return;
            }

            Stubs::makeDirectory('routes', $flags);
            Stubs::copy('laravel-routes-web.php', 'routes/web.php', $flags);
            Stubs::fillServiceProviderStub('ROUTES', 'laravel-boot-routes.php', $flags);
        });

        // Migrations
        $this->task('Migrations', function () use ($flags) {
            if (! $flags->hasParam('migrations')) {
                return;
            }

            Stubs::makeDirectory('database', $flags);
            Stubs::makeDirectory('database/migrations', $flags);
            Stubs::copy('.gitkeep', 'database/migrations/.gitkeep', $flags);
            Stubs::fillServiceProviderStub('MIGRATIONS', 'laravel-boot-migrations.php', $flags);
        });

        $this->task('Swapping namespaces, classes, etc', function () use ($flags) {
            collect(File::allFiles($flags->packageDirectory()))
                ->each(function (SplFileInfo $file) use ($flags) {
                    $contents = $file->getContents();

                    // Vendor
                    $contents = str_replace('DummyVendor', Str::studly($flags->vendorName()), $contents);
                    $contents = str_replace('dummy-vendor', $flags->vendorName(), $contents);

                    // Package Name
                    $contents = str_replace('DummyPackage', Str::studly($flags->packageName()), $contents);
                    $contents = str_replace('dummy-package', $flags->packageName(), $contents);

                    // Classes
                    $contents = str_replace('DummyPackageServiceProvider', Str::studly($flags->packageName()).'ServiceProvider', $contents);
                    $contents = str_replace('DummyPackageFacade', Str::studly($flags->packageName()).'Facade', $contents);

                    if ($file->getFilename() === 'DummyPackageServiceProvider.php') {
                        $contents = str_replace('#CONFIG#', '', $contents);
                        $contents = str_replace('#VIEWS#', '', $contents);
                        $contents = str_replace('#LANG#', '', $contents);
                        $contents = str_replace('#ROUTES#', '', $contents);
                        $contents = str_replace('#MIGRATIONS#', '', $contents);

                        File::put($file->getPath().'/'.Str::studly($flags->packageName()).'ServiceProvider.php', $contents);
                        File::delete($file->getPathname());

                        return;
                    }

                    if ($file->getFilename() === 'DummyPackageFacade.php') {
                        File::put($file->getPath().'/'.Str::studly($flags->packageName()).'Facade.php', $contents);
                        File::delete($file->getPathname());

                        return;
                    }

                    File::put($file->getPathname(), $contents);
                });

            // TODO: Run PHP CS Fixer (or some other linter) to fix service provider indentation
        });

        $this->task('Composer Install', function () use ($flags) {
            $this->line('');

            $process = new Process(['composer install'], $flags->packageDirectory());
            $process->run();
        });
    }
}
