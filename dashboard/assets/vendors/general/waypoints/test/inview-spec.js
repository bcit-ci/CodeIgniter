'use strict'
/* global
 * describe, it, beforeEach, afterEach, expect, spyOn, waits, runs,
 * waitsFor, loadFixtures, Waypoint
 */

window.jQuery.each(Waypoint.adapters, function(i, adapter) {
  describe(adapter.name + ' adapter:', function() {
    describe('Waypoints Inview Shortcut', function() {
      var $ = window.jQuery
      var standard = 50
      var $scroller = $(window)
      var $target, waypoint, hits, callbackContext

      function setsTrue(key) {
        return function() {
          callbackContext = this
          hits[key] = true
        }
      }

      function toBeTrue(key) {
        return function() {
          return hits[key]
        }
      }

      beforeEach(function() {
        Waypoint.Adapter = adapter.Adapter
        loadFixtures('standard.html')
        $target = $('#near2')
        hits = {}
      })

      afterEach(function() {
        waypoint.destroy()
        $scroller.scrollTop(0).scrollLeft(0)
      })

      describe('vertical', function() {
        beforeEach(function() {
          waypoint = new Waypoint.Inview({
            element: $target[0],
            enter: setsTrue('enter'),
            entered: setsTrue('entered'),
            exit: setsTrue('exit'),
            exited: setsTrue('exited')
          })
        })

        describe('enter callback', function() {
          it('triggers when element starts entering from below', function() {
            runs(function() {
              var top = $target.offset().top
              $scroller.scrollTop(top - Waypoint.viewportHeight())
            })
            waitsFor(toBeTrue('enter'), 'enter to trigger')
            runs(function() {
              expect(callbackContext).toEqual(waypoint)
            })
          })

          it('triggers when element starts entering from above', function() {
            runs(function() {
              $scroller.scrollTop($target.offset().top + $target.outerHeight())
            })
            waits(standard)
            runs(function() {
              hits.enter = false
              $scroller.scrollTop($scroller.scrollTop() - 1)
            })
            waitsFor(toBeTrue('enter'), 'enter to trigger')
          })
        })

        describe('entered callback', function() {
          it('triggers when element finishes entering from below', function() {
            runs(function() {
              var top = $target.offset().top
              var viewportHeight = Waypoint.viewportHeight()
              var elementHeight = $target.outerHeight()
              $scroller.scrollTop(top - viewportHeight + elementHeight)
            })
            waitsFor(toBeTrue('entered'), 'entered to trigger')
            runs(function() {
              expect(callbackContext).toEqual(waypoint)
            })
          })

          it('triggers when element finishes entering from above', function() {
            runs(function() {
              $scroller.scrollTop($target.offset().top)
            })
            waits(standard)
            runs(function() {
              hits.entered = false
              $scroller.scrollTop($scroller.scrollTop() - 1)
            })
            waitsFor(toBeTrue('entered'), 'entered to trigger')
          })
        })

        describe('exit callback', function() {
          it('triggers when element starts leaving below', function() {
            runs(function() {
              var top = $target.offset().top
              var viewportHeight = Waypoint.viewportHeight()
              var elementHeight = $target.outerHeight()
              $scroller.scrollTop(top - viewportHeight + elementHeight)
            })
            waits(standard)
            runs(function() {
              expect(hits.exit).toBeFalsy()
              $scroller.scrollTop($scroller.scrollTop() - 1)
            })
            waitsFor(toBeTrue('exit'), 'exit to trigger')
          })

          it('triggers when element starts leaving above', function() {
            runs(function() {
              $scroller.scrollTop($target.offset().top)
            })
            waitsFor(toBeTrue('exit'), 'exit to trigger')
            runs(function() {
              expect(callbackContext).toEqual(waypoint)
            })
          })
        })

        describe('exited callback', function() {
          it('triggers when element finishes exiting below', function() {
            runs(function() {
              var top = $target.offset().top
              $scroller.scrollTop(top - Waypoint.viewportHeight())
            })
            waits(standard)
            runs(function() {
              $scroller.scrollTop($scroller.scrollTop() - 1)
            })
            waitsFor(toBeTrue('exited'), 'exited to trigger')
          })

          it('triggers when element finishes exiting above', function() {
            runs(function() {
              $scroller.scrollTop($target.offset().top + $target.outerHeight())
            })
            waitsFor(toBeTrue('exited'), 'exited to trigger')
            runs(function() {
              expect(callbackContext).toEqual(waypoint)
            })
          })
        })
      })

      describe('horizontal', function() {
        beforeEach(function() {
          waypoint = new Waypoint.Inview({
            horizontal: true,
            element: $target[0],
            enter: setsTrue('enter'),
            entered: setsTrue('entered'),
            exit: setsTrue('exit'),
            exited: setsTrue('exited')
          })
        })

        describe('enter callback', function() {
          it('triggers when element starts entering from right', function() {
            runs(function() {
              $scroller.scrollLeft($target.offset().left - $scroller.width())
            })
            waitsFor(toBeTrue('enter'), 'enter to trigger')
          })

          it('triggers when element starts entering from left', function() {
            runs(function() {
              var left = $target.offset().left
              $scroller.scrollLeft(left + $target.outerWidth())
            })
            waits(standard)
            runs(function() {
              hits.enter = false
              $scroller.scrollLeft($scroller.scrollLeft() - 1)
            })
            waitsFor(toBeTrue('enter'), 'enter to trigger')
          })
        })

        describe('entered callback', function() {
          it('triggers when element finishes entering from right', function() {
            runs(function() {
              var left = $target.offset().left
              var viewportWidth = $scroller.width()
              var elementWidth = $target.outerWidth()
              $scroller.scrollLeft(left - viewportWidth + elementWidth)
            })
            waitsFor(toBeTrue('entered'), 'entered to trigger')
          })

          it('triggers when element finishes entering from left', function() {
            runs(function() {
              $scroller.scrollLeft($target.offset().left)
            })
            waits(standard)
            runs(function() {
              hits.entered = false
              $scroller.scrollLeft($scroller.scrollLeft() - 1)
            })
            waitsFor(toBeTrue('entered'), 'entered to trigger')
          })
        })

        describe('exit callback', function() {
          it('triggers when element starts leaving on the right', function() {
            runs(function() {
              var left = $target.offset().left
              var viewportWidth = $scroller.width()
              var elementWidth = $target.outerWidth()
              $scroller.scrollLeft(left - viewportWidth + elementWidth)
            })
            waits(standard)
            runs(function() {
              expect(hits.exit).toBeFalsy()
              $scroller.scrollLeft($scroller.scrollLeft() - 1)
            })
            waitsFor(toBeTrue('exit'), 'exit to trigger')
          })

          it('triggers when element starts leaving on the left', function() {
            runs(function() {
              $scroller.scrollLeft($target.offset().left)
            })
            waitsFor(toBeTrue('exit'), 'exit to trigger')
          })
        })

        describe('exited callback', function() {
          it('triggers when element finishes exiting to the right', function() {
            runs(function() {
              var left = $target.offset().left
              $scroller.scrollLeft(left - $scroller.width())
            })
            waitsFor(toBeTrue('enter'), 'enter to trigger')
            runs(function() {
              $scroller.scrollLeft($scroller.scrollLeft() - 1)
            })
            waitsFor(toBeTrue('exited'), 'exited to trigger')
          })

          it('triggers when element finishes exiting to the left', function() {
            runs(function() {
              var left = $target.offset().left
              $scroller.scrollLeft(left + $target.outerWidth())
            })
            waitsFor(toBeTrue('exited'), 'exited to trigger')
          })
        })
      })

      describe('disabled', function() {
        beforeEach(function() {
          waypoint = new Waypoint.Inview({
            element: $target[0],
            enabled: false,
            enter: setsTrue('enter'),
            entered: setsTrue('entered'),
            exit: setsTrue('exit'),
            exited: setsTrue('exited')
          })
        })

        it('starts disabled', function() {
          $.each(waypoint.waypoints, function(i, wp) {
            expect(wp.enabled).toEqual(false)
          })
        })

        describe('#enable', function() {
          it('enables all waypoints', function() {
            waypoint.enable()
            $.each(waypoint.waypoints, function(i, wp) {
              expect(wp.enabled).toEqual(true)
            })
          })
        })

        describe('#disable', function() {
          it('disables all waypoints', function() {
            waypoint.enable()
            waypoint.disable()
            $.each(waypoint.waypoints, function(i, wp) {
              expect(wp.enabled).toEqual(false)
            })
          })
        })
      })
    })
  })
})
