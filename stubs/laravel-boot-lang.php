$this->publishes([
    __DIR__.'/../resources/lang' => resource_path('lang/vendor/dummy-package'),
], 'dummy-package-translations');

$this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'dummy-package');
