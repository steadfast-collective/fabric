<?php

namespace App;

use Illuminate\Support\Str;

class Flags
{
    protected $name;
    protected $params;

    public function __construct(string $name, array $params)
    {
        $this->name = $name;
        $this->params = $params;
    }

    public function vendorName(): string
    {
        return explode('/', $this->name)[0];
    }

    public function packageName(): string
    {
        return explode('/', $this->name)[1];
    }

    public function packageDirectory(): string
    {
        return getcwd().'/'.Str::slug($this->packageName);
    }

    public function packageNamespace(): string
    {
        return Str::studly($this->vendorName().'\\'.Str::studly($this->packageDirectory()));
    }

    public function hasParam(string $paramName)
    {
        return in_array($paramName, $this->params);
    }
}
