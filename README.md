# Fabric

Fabric is a command-line utility to quickly scaffold PHP and Laravel packages.

## Installation

```
composer global require steadfastcollective/fabric
```

## Usage

Make sure `~/.composer/vendor/bin` is in your terminal's path.

### PHP

```
cd ~/Code
fabric php steadfastcollective/vesta-php --tests
```

**Parameters**

| Name         | Description                                                                     |
|--------------|---------------------------------------------------------------------------------|
| --tests      | Scaffolds out a basic testing setup, with PHPUnit.                              |

### Laravel

```
cd ~/Code
fabric laravel steadfastcollective/cashier-extended --tests --facade --config
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

### Statamic

> Fabric doesn't currently provide the ability to provide everything you might need (fieldtypes, filters, widgets, etc). In those cases, it may be a good idea to create the addon with Fabric and then create the types using Statamic's `please` command line tool.

```
cd ~/Code
fabric laravel steadfastcollective/twitter-sharing-tool --tests --config --action
```

**Parameters**

| Name         | Description                                                                     |
|--------------|---------------------------------------------------------------------------------|
| --tests      | Scaffolds out a basic testing setup, with PHPUnit.                              |
| --config     | Creates a configuration file and hooks it up in your service provider.          |
| --views      | Creates an empty views directory and hooks it up in your service provider.      |
| --lang       | Creates an empty lang directory and hooks it up in your service provider.       |
| --routes     | Creates an empty routes file and hooks it up in your service provider.          |
| --modifier   | Creates a modifier class and hooks it up in your service provider.              |
| --action     | Creates an action class and hooks it up in your service provider.               |
