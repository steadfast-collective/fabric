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

        $this->clonedPath = $_SERVER['HOME'].'/.fabric_temp';
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
        return Str::studly($this->vendorName().'\\'.Str::studly($this->packageDirectory()));
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
        return $this->params[$paramName];
    }

    public function setParam(string $param, $value): self
    {
        $this->params[$param] = $value;

        return $this;
    }

    public function hasParam(string $paramName)
    {
        return in_array($paramName, $this->params);
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
        if ($params = []) {
            return $this->params;
        }

        $this->params = array_merge($params, $this->params);
    }
}
