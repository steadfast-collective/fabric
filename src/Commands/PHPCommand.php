<?php

namespace SteadfastCollective\Fabric\Commands;

use SteadfastCollective\Fabric\Flags;
use SteadfastCollective\Fabric\Stubs;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Finder\SplFileInfo;

class PHPCommand extends Command
{
    protected $signature = 'php {name} {--tests}';
    protected $description = 'Bootstrap a PHP package.';

    public function handle()
    {
        if (! str_contains($this->argument('name'), '/')) {
            return $this->error("Invalid package name [{$this->argument('name')}]. Please correct it and try again. (eg. steadfastcollective/fabric)");
        }

        $flags = new Flags($this->argument('name'), [
            'tests' => $this->option('tests'),
        ]);

        if ($flags->hasEmptyParams()) {
            $flags->setParam('tests', $this->confirm('Should we setup PHPUnit for you?'));
        }

        $this->task('Copying stubs', function () use ($flags) {
            Stubs::downloadStubs($flags);
            Stubs::copyDirectory('php', '.', $flags);
        });

        // Tests
        $this->task('Tests', function () use ($flags) {
            if (! $flags->hasParam('tests')) {
                return;
            }

            Stubs::copyDirectory('php-tests/tests', 'tests', $flags);
            Stubs::copy('php-tests/phpunit.xml', 'phpunit.xml', $flags);
            Stubs::mergeManifest([
                'autoload-dev' => [
                    'psr-4' => [
                        Str::studly($flags->vendorName())."\\".Str::studly($flags->packageName())."\\Tests\\" => "tests",
                    ],
                ],
                'require-dev' => [
                    'phpunit/phpunit' => "^9.3",
                ],
            ], $flags);
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
                    $contents = str_replace('DummyClass', Str::studly($flags->packageName()), $contents);

                    if ($file->getFilename() === 'DummyClass.php') {
                        File::put($file->getPath().'/'.Str::studly($flags->packageName()).'.php', $contents);
                        File::delete($file->getPathname());

                        return;
                    }

                    File::put($file->getPathname(), $contents);
                });
        });

        $this->task('Composer Install', function () use ($flags) {
            $this->line('');

            $process = new Process(['composer install'], $flags->packageDirectory());
            $process->run();
        });

        $this->task('Cleaning up', function () use ($flags) {
            Stubs::deleteStubs($flags);
        });
    }
}
