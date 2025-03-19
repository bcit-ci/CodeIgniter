# Doctrine Annotations

[![Build Status](https://travis-ci.org/doctrine/annotations.png?branch=master)](https://travis-ci.org/doctrine/annotations)

Docblock Annotations Parser library (extracted from [Doctrine Common](https://github.com/doctrine/common)).

## Changelog

### v1.2.0

 * HHVM support
 * Allowing dangling comma in annotations
 * Excluded annotations are no longer autoloaded
 * Importing namespaces also in traits
 * Added support for `::class` 5.5-style constant, works also in 5.3 and 5.4

### v1.1

 * Add Exception when ZendOptimizer+ or Opcache is configured to drop comments
