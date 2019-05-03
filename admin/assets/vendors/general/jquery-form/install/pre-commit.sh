#!/bin/sh
#
# Pre-commit hooks

# Make sure node modules are available to Github Desktop
PATH=$PATH:/usr/local/bin:/usr/local/sbin

# Lint and test before committing
grunt test
