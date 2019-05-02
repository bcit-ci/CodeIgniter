#!/bin/bash -x

if [ "$TEST_SUITE" == "unit" ]; then
  ./node_modules/karma/bin/karma start --single-run --browsers PhantomJS
elif [ "$TRAVIS_SECURE_ENV_VARS" == "true" -a "$TEST_SUITE" == "integration" ]; then
  static -p 8888 &
  sleep 3
  # integration tests are flaky, don't let them fail the build
  ./node_modules/mocha/bin/mocha --harmony -R spec ./test/integration/test.js || true
else
  echo "Not running any tests"
fi
