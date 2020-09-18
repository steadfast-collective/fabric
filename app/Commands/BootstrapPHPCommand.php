<?php

namespace App\Commands;

use App\Flags;
use App\Stubs;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class BootstrapPHPCommand extends Command
{
    protected $signature = 'php {name} {--tests}';
    protected $description = 'Bootstrap a PHP package.';

    public function handle()
    {
        $flags = new Flags($this->argument('name'), [
            'tests' => $this->option('tests'),
        ]);

        if ($flags->hasEmptyParams()) {
            $params['tests'] = $this->confirm('Should we setup PHPUnit for you?');

            $flags->params($params);
        }

        $this->task('Copying stubs', function () use ($flags) {
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
    }
}
