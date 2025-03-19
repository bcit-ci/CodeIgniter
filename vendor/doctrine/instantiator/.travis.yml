language: php

php:
  - 5.3.3
  - 5.3
  - 5.4
  - 5.5
  - 5.6
  - hhvm

before_script:
  - ./.travis.install.sh
  - if [ $TRAVIS_PHP_VERSION = '5.6' ]; then PHPUNIT_FLAGS="--coverage-clover coverage.clover"; else PHPUNIT_FLAGS=""; fi

script:
  - if [ $TRAVIS_PHP_VERSION = '5.3.3' ]; then phpunit; fi
  - if [ $TRAVIS_PHP_VERSION != '5.3.3' ]; then ./vendor/bin/phpunit $PHPUNIT_FLAGS; fi
  - if [ $TRAVIS_PHP_VERSION != '5.3.3' ]; then ./vendor/bin/phpcs --standard=PSR2 ./src/ ./tests/; fi
  - if [[ $TRAVIS_PHP_VERSION != '5.3.3' && $TRAVIS_PHP_VERSION != '5.4.29' && $TRAVIS_PHP_VERSION != '5.5.13' ]]; then php -n ./vendor/bin/athletic -p ./tests/DoctrineTest/InstantiatorPerformance/ -f GroupedFormatter; fi

after_script:
  - if [ $TRAVIS_PHP_VERSION = '5.6' ]; then wget https://scrutinizer-ci.com/ocular.phar; php ocular.phar code-coverage:upload --format=php-clover coverage.clover; fi
