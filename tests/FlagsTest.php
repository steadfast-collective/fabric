<?php

namespace SteadfastCollective\Fabric\Tests;

use SteadfastCollective\Fabric\Flags;

class FlagsTest extends TestCase
{
    protected $flags;

    public function setUp(): void
    {
        parent::setUp();

        $this->flags = new Flags('steadfastcollective/laravel-penguin', [
            'tests' => false,
        ]);
    }

    /** @test */
    public function can_get_vendor_name()
    {
        $this->assertSame('steadfastcollective', $this->flags->vendorName());
    }

    /** @test */
    public function can_get_package_name()
    {
        $this->assertSame('laravel-penguin', $this->flags->packageName());
    }

    /** @test */
    public function can_get_package_directory()
    {
        $this->assertSame(getcwd().'/laravel-penguin', $this->flags->packageDirectory());
    }

    /** @test */
    public function can_get_package_namespace()
    {
        $this->assertSame('Steadfastcollective\LaravelPenguin', $this->flags->packageNamespace());
    }

    /** @test */
    public function can_get_cloned_path()
    {
        $this->assertStringContainsString('/', realpath($this->flags->clonedStubsPath()));
    }

    /** @test */
    public function can_get_cloned_stubs_path()
    {
        $this->assertStringContainsString('/stubs', realpath($this->flags->clonedStubsPath()));
    }

    /** @test */
    public function can_get_param()
    {
        $this->assertFalse($this->flags->getParam('tests'));
    }

    /** @test */
    public function can_set_param()
    {
        $this->assertFalse($this->flags->getParam('tests'));

        $this->flags->setParam('tests', true);
        $this->flags->setParam('somethingElse', 'value');

        $this->assertTrue($this->flags->getParam('tests'));
        $this->assertSame('value', $this->flags->getParam('somethingElse'));
    }

    /** @test */
    // Ignore the incorrect grammar in this method name
    public function can_get_if_we_has_param()
    {
        $this->assertTrue($this->flags->hasParam('tests'));
        $this->assertFalse($this->flags->hasParam('vapor-config'));
    }

    /** @test */
    public function can_get_if_we_has_empty_params()
    {
        $tempFlags = new Flags('steadfastcollective/christmas', []);

        $this->assertTrue($tempFlags->hasEmptyParams());
    }

    /** @test */
    public function can_get_name()
    {
        $this->assertSame('steadfastcollective/laravel-penguin', $this->flags->name());
    }

    /** @test */
    public function can_get_params()
    {
        $this->assertIsArray($this->flags->params());
    }
}
