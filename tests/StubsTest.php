<?php

namespace SteadfastCollective\Fabric\Tests;

use Illuminate\Support\Facades\File;
use SteadfastCollective\Fabric\Flags;
use SteadfastCollective\Fabric\ProcessService;
use SteadfastCollective\Fabric\Stubs;

class StubsTest extends TestCase
{
    protected Flags $flags;
    protected Stubs $stubs;

    public function setUp(): void
    {
        parent::setUp();

        $this->flags = new Flags('steadfastcollective/slackbot', []);
        $this->stubs = new Stubs();
    }

    /** @test */
    public function can_download_stubs()
    {
        $this->markTestIncomplete();

        // TODO: figure out how to test stub downloading
        $download = $this->stubs->downloadStubs($this->flags);
    }

    /** @test */
    public function can_delete_stubs()
    {
        File::shouldReceive('deleteDirectory')
            ->once()
            ->with($this->flags->clonedPath())
            ->andReturn(true);

        $this->stubs->deleteStubs($this->flags);
    }

    /** @test */
    public function can_make_directory()
    {
        File::shouldReceive('exists')
            ->once()
            ->with($this->flags->packageDirectory().'/migrations')
            ->andReturn(false);

        File::shouldReceive('makeDirectory')
            ->once()
            ->with($this->flags->packageDirectory().'/migrations')
            ->andReturn(true);

        $this->stubs->makeDirectory('migrations', $this->flags);
    }

    /** @test */
    public function can_copy_directory()
    {
        File::shouldReceive('copyDirectory')
            ->once()
            ->with($this->flags->clonedStubsPath().'/php-tests', $this->flags->packageDirectory().'/tests')
            ->andReturn(true);

        $this->stubs->copyDirectory('php-tests', 'tests', $this->flags);
    }

    /** @test */
    public function can_copy_file()
    {
        File::shouldReceive('copy')
            ->once()
            ->with($this->flags->clonedStubsPath().'/php-tests/phpunit.xml', $this->flags->packageDirectory().'/phpunit.xml')
            ->andReturn(true);

        $this->stubs->copy('php-tests/phpunit.xml', 'phpunit.xml', $this->flags);
    }

    /** @test */
    public function can_merge_manifest()
    {
        if (! File::exists($this->flags->packageDirectory())) {
            File::makeDirectory($this->flags->packageDirectory());
        }

        file_put_contents($this->flags->packageDirectory().'/composer.json', file_get_contents(__DIR__.'/../stubs/php/composer.json'));

        $this->assertArrayNotHasKey('require-dev', json_decode(file_get_contents($this->flags->packageDirectory().'/composer.json'), true));

        $this->stubs->mergeManifest([
            'require-dev' => [
                'orchestra/testbench' => '^4.0|^5.0|^6.0',
                'phpunit/phpunit' => '^8.0|^9.0',
            ],
        ], $this->flags);

        $this->assertArrayHasKey('require-dev', json_decode(file_get_contents($this->flags->packageDirectory().'/composer.json'), true));
    }

    /** @test */
    public function can_fill_service_provider_stub()
    {
        if (! File::exists($this->flags->packageDirectory().'/src')) {
            File::makeDirectory($this->flags->packageDirectory().'/src');
        }

        file_put_contents($this->flags->packageDirectory().'/src/DummyPackageServiceProvider.php', file_get_contents(__DIR__.'/../stubs/laravel/src/DummyPackageServiceProvider.php'));

        $this->stubs->fillServiceProviderStub('CONFIG', 'laravel-boot-config.php', $this->flags);

        $this->assertStringContainsString('$this->mergeConfigFrom(', file_get_contents($this->flags->packageDirectory().'/src/DummyPackageServiceProvider.php'));
    }
}
