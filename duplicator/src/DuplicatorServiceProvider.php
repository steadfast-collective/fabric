<?php

namespace Damcclean\Duplicator;

use Illuminate\Support\ServiceProvider;

class DuplicatorServiceProvider extends ServiceProvider
{
    

    public function boot()
    {
        
        
        
        
        Actions\DuplicatorAction::register();

        //
    }

    public function register()
    {
        //
    }
}
