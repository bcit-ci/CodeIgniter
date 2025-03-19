language: php

sudo: false

cache:
  directory:
    - $HOME/.composer/cache

php:
  - 5.3
  - 5.4
  - 5.5
  - 5.6
  - 7.0
  - hhvm

install:
  - composer install -n

script:
  - phpunit
