# Fabric

Fabric is a command-line utility to quickly scaffold PHP and Laravel packages. This tool is still in early stages, any bugs can be reported as [Github Issues](https://github.com/steadfast-collective/fabric/issues).

## Installation

It's recommended to install Fabric globally on your machine so you can run `fabric` commands where-ever you are in your terminal.

```
composer global require steadfastcollective/fabric
```

You'll also want to ensure that `~/.composer/vendor/bin` is in your terminal's path.

## Usage

Fabric provides seperate commands for each type of package that can be bootstrapped. One for `php`, one for `laravel` and one for `statamic`. You can specify when you run the command, like so: `fabric php`

When running a Fabric command, you'll also need to tell it the [name of the Composer package](https://getcomposer.org/doc/04-schema.md#name) you want to bootstrap. For example: `fabric php steadfastcollective/package-name`, where `steadfastcollective` is the Packagist vendor and `package-name` is the name of your package.

You can optionally provide a set of parameters which will be used to tell Fabric about any specific things you'd like to be bootstrapped, like Tests or a Facade. The list of parameters is documented for each package type. If you don't provide any parameters, you'll be presented with a yes/no wizard instead.

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
fabric statamic steadfastcollective/twitter-sharing-tool --tests --config --action
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

## Development

If you're wanting to work on Fabric (the tool) locally, there's a few steps to getting it setup:

1. Clone this repository: `git clone git@github.com:steadfast-collective/fabric.git`
2. Change directory into `fabric`
3. Install Composer dependencies `composer install`

After the above steps, you'll be able to run Fabric commands with `php fabric`. This will also allow you to run two versions of Fabric, the production one which is globally installed with Composer and the development version, cloned to your machine.
