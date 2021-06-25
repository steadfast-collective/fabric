# Changelog

## Unreleased

## v1.0.8 (2021-06-25)

* Indentation has finally been fixed #1
* An error will now be thrown if an invalid package name is given #7
* If a directory already exists for the intended destination, throw an error.

## v1.0.7 (2021-06-01)

* Added support for PHP 8
* Fixed some type hinting issues for PHP 7.3 users

## v1.0.6 (2020-10-30)

* Fixed the optional wizard, where chosen options were being forgotten about.
* Fixed bug where the package namespace would be generated wrong
* Possibly also fixed a bunch of other bugs 🐛

## v1.0.5 (2020-10-05)

* Fixed issue where config file was not being renamed from stubs.

## v1.0.4 (2020-10-02)

* Another path bugfix

## v1.0.3 (2020-10-02)

* Fixed issue with getting stubs, they'll now be cloned down during the generation process.

## v1.0.2 (2020-09-25)

* Fixed issue where dependencies wouldn't be built into binary 🤦‍♂️

## v1.0.1 (2020-09-25)

* Fixed some composer dependency stuff

## v1.0.0 (2020-09-25)

**Initial release of Fabric, a package bootstrapper for PHP, Laravel and Statamic. 🎉**
