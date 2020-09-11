<?php

namespace DummyVendor\DummyPackage;

use Illuminate\Support\Facades\Facade;

class DummyPackageFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dummy-package';
    }
}
