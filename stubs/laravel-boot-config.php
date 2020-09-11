$this->publishes([
    __DIR__.'/../config/dummy-package.php' => config_path('dummy-package.php'),
], 'dummy-package-config');

$this->mergeConfigFrom(__DIR__.'/../config/dummy-package.php', 'dummy-package');
