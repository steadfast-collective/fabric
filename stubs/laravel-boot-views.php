$this->publishes([
    __DIR__.'/../resources/views' => resource_path('views/vendor/dummy-package'),
], 'dummy-package-views');

$this->loadViewsFrom(__DIR__.'/../resources/views', 'dummy-package');
