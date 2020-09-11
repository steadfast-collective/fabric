<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Support\Str;

class BootstrapPHPCommand extends Command
{
    protected $signature = 'php {name} {--tests}';
    protected $description = 'Bootstrap a PHP package.';

    protected $flags;
    protected $vendorName;
    protected $packageName;
    protected $packageDirectory;
    protected $packageNamespace;

    public function handle()
    {
        $this->flags = [
            'name'   => $this->argument('name'),
            'params' => $this->getOptions(),
        ];

        $this->vendorName = explode('/', $this->flags['name'])[0];
        $this->packageName = explode('/', $this->flags['name'])[1];
        $this->packageDirectory = getcwd().'/'.Str::slug($this->packageName);
        $this->packageNamespace = Str::studly($this->vendorName).'\\'.Str::studly($this->packageName);

        // TODO: wizard (if no params are set)

        $this->task('Copying stubs', function () {
            File::copyDirectory(STUBS_DIRECTORY.'/php', $this->packageDirectory);
        });

        $this->task('Updating code', function () {
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

        $this->task('Installing Composer Dependencies', function () {
            $this->line('');
            shell_exec("cd {$this->packageDirectory} && composer install");
        });
    }
}
