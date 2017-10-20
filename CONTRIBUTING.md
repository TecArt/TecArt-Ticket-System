# Contributing

First of all - thank you for taking the time to contribute!

The following text will explain how to set up a development environment and 
what to 

*Important:* If you find and/or fix security issues, please disclose these 
before committing or PRing them. You can find contact information in 
[CONTACT](CONTACT.md)

    The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL
    NOT", "SHOULD", "SHOULD NOT", "RECOMMENDED",  "MAY", and
    "OPTIONAL" in this document are to be interpreted as described in
    RFC 2119.

## Setup

The software is tested with Apache2 and PHP 7.1 - other Webserver Stacks might
not work properly and may need fixing.

  * Copy the example configuration under config/config.php.example to
    config/config.php
  * Edit config/config.inc.php so it fits to your development system

## Coding Style

New code MUST adhere to [PSR-1](http://www.php-fig.org/psr/psr-1/) and
[PSR-2](http://www.php-fig.org/psr/psr-2/).

New functions and classes and altered functions and classes MUST contain
docblock comments adhering to the
[PHPDoc](http://docs.phpdoc.org/references/phpdoc/index.html) standard, even if
they didn't have comments before.
