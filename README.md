# Package Bootstrapper

Package Bootstrapper is a command line utility you can use to quickly scaffold out PHP and [Laravel](https://laravel.com) packages.

## Installation

```
composer global require steadfastcollective/package-bootstrapper
```

## Usage

Make sure `~/.composer/vendor/bin` is in your terminal's path.

### PHP

```
cd ~/Code
package-bootstrapper php steadfastcollective/cashier-extended --tests
```

**Parameters**

| Name         | Description                                                                     |
|--------------|---------------------------------------------------------------------------------|
| --tests      | Scaffolds out a basic testing setup, with PHPUnit.                              |

### Laravel

```
cd ~/Code
package-bootstrapper laravel steadfastcollective/cashier-extended --tests --facade --config
```

**Parameters**

| Name         | Description                                                                     |
|--------------|---------------------------------------------------------------------------------|
| --tests      | Scaffolds out a basic testing setup, with PHPUnit.                              |
| --facade     | Creates a Facade for your package.                                              |
| --config     | Creates a configuration file and hooks it up in your service provider.          |
| --views      | Creates an empty views directory and hooks it up in your service provider.      |
| --lang       | Creates an empty lang directory and hooks it up in your service provider.       |
| --routes     | Creates an empty routes file and hooks it up in your service provider.          |
| --migrations | Creates an empty migrations directory and hooks it up in your service provider. |
