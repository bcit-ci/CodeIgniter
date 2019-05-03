/* global jasmine, Waypoint */

'use strict'

jasmine.getFixtures().fixturesPath = 'test/fixtures'
jasmine.getEnv().defaultTimeoutInterval = 1000
Waypoint.requestAnimationFrame = function(callback) {
  callback()
}
