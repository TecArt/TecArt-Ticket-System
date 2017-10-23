# Contributing

First of all - thank you for taking the time to contribute!

The following text will explain how to set up a development environment.

*Important:* If you find and/or fix security issues, please disclose these 
before committing or PRing them. You can find contact information in 
[CONTACT](CONTACT.md)

    The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL
    NOT", "SHOULD", "SHOULD NOT", "RECOMMENDED",  "MAY", and
    "OPTIONAL" in this document are to be interpreted as described in
    RFC 2119.

## Setup

The software is tested with Apache2 and PHP 7.1 - other Webserver Stacks might
not work properly and may need fixing. If you want to contribute support for
a different webserver stack please also include necessary webserver
configuration files in your pull request.

  * Clone the repository to a folder that is set up to be served by your
    webserver
  * Copy the example configuration under config/config.php.example to
    config/config.php
  * Edit config/config.inc.php so it fits to your development system
  * Navigate your browser to the location configured in your webserver

## Coding Style

New code MUST adhere to [PSR-1](http://www.php-fig.org/psr/psr-1/) and
[PSR-2](http://www.php-fig.org/psr/psr-2/).

New functions and classes and altered functions and classes MUST contain
docblock comments adhering to the
[PHPDoc](http://docs.phpdoc.org/references/phpdoc/index.html) standard, even if
they didn't have comments before.

## Translations

To translate the software to another language, create a new file in the `lang/` folder with the name pattern `XX.php` XX being the shortcode for the language(IETF language code for country-specific translations or ISO 639 two-letter code for generic translations).

The easiest way to start out is to copy the English translation and start from there. To test your translation, change the language setting in [`config/config.php`](https://github.com/TecArt/TecArt-Ticket-System/blob/8b2932d5a30ab61f362113e1880aa905c674e07a/config/config.php.sample#L3) to the tag for your language.
