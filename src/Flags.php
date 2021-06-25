<?php

namespace SteadfastCollective\Fabric;

use Illuminate\Support\Str;

class Flags
{
    protected $name;
    protected $params;
    protected $clonedPath;

    public function __construct(string $name, array $params)
    {
        $this->name = $name;
        $this->params = $params;

        if (config('app.env') === 'testing') {
            $this->clonedPath = __DIR__.'/..';
        } else {
            $this->clonedPath = $_SERVER['HOME'].'/.fabric_temp';
        }
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
        return getcwd().'/'.Str::slug($this->packageName());
    }

    public function packageNamespace(): string
    {
        return Str::studly($this->vendorName().'\\'.Str::studly($this->packageName()));
    }

    public function clonedPath()
    {
        return $this->clonedPath;
    }

    public function clonedStubsPath()
    {
        return $this->clonedPath.'/stubs';
    }

    public function getParam(string $paramName)
    {
        if (! $this->hasParam($paramName)) {
            return null;
        }

        return $this->params[$paramName];
    }

    public function setParam(string $paramName, $value): self
    {
        $this->params[$paramName] = $value;

        return $this;
    }

    public function hasParam(string $paramName)
    {
        return isset($this->params[$paramName]);
    }

    public function hasEmptyParams(): bool
    {
        foreach ($this->params as $key => $value) {
            if ($value === true) {
                return false;
            }
        }

        return true;
    }

    public function name(string $name = '')
    {
        if ($name === '') {
            return $this->name;
        }

        $this->name = $name;
    }

    public function params(array $params = [])
    {
        if ($params === []) {
            return $this->params;
        }

        $this->params = array_merge($params, $this->params);
    }
}
