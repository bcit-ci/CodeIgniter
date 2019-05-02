'use strict'
/* global
 * describe, it, beforeEach, afterEach, expect, spyOn, waits, runs,
 * waitsFor, loadFixtures, Waypoint, jasmine
 */

describe('Waypoint Sticky Shortcut', function() {
  var $ = window.jQuery
  var $scroller = $(window)
  var $sticky, waypoint, handlerSpy

  beforeEach(function() {
    loadFixtures('sticky.html')
    $sticky = $('.sticky')
  })

  describe('with default options', function() {
    beforeEach(function() {
      handlerSpy = jasmine.createSpy('on handler')
      waypoint = new Waypoint.Sticky({
        element: $sticky[0],
        handler: handlerSpy
      })
    })

    afterEach(function() {
      if (waypoint) {
        waypoint.destroy()
      }
      $scroller.scrollTop(0)
    })

    describe('on init', function() {
      afterEach(function() {
        waypoint.destroy()
      })

      it('returns an instance of the Waypoint.Sticky class', function() {
        expect(waypoint instanceof Waypoint.Sticky).toBeTruthy()
      })

      it('wraps the sticky element on init', function() {
        expect($sticky.parent()).toHaveClass('sticky-wrapper')
      })

      describe('when sticky element is scrolled to', function() {
        beforeEach(function() {
          runs(function() {
            $scroller.scrollTop($sticky.offset().top)
          })
          waitsFor(function() {
            return $sticky.hasClass('stuck')
          }, 'stuck class to apply')
        })

        it('adds/removes stuck class', function() {
          runs(function() {
            $scroller.scrollTop($scroller.scrollTop() - 1)
          })
          waitsFor(function() {
            return !$sticky.hasClass('stuck')
          })
        })

        it('gives the wrapper the same height as the sticky element', function() {
          expect($sticky.parent().height()).toEqual($sticky.outerHeight())
        })

        it('executes handler option after stuck class applied', function() {
          expect(handlerSpy).toHaveBeenCalled()
        })
      })
    })

    describe('#destroy', function() {
      beforeEach(function() {
        runs(function() {
          $scroller.scrollTop($sticky.offset().top)
        })
        waitsFor(function() {
          return handlerSpy.callCount
        })
        runs(function() {
          waypoint.destroy()
        })
      })

      it('unwraps the sticky element', function() {
        expect($sticky.parent()).not.toHaveClass('sticky-wrapper')
      })

      it('removes the stuck class', function() {
        expect($sticky).not.toHaveClass('stuck')
      })
    })
  })

  describe('with wrapper false', function() {
    beforeEach(function() {
      waypoint = new Waypoint.Sticky({
        element: $sticky[0],
        handler: handlerSpy,
        wrapper: false
      })
    })

    it('does not wrap the sticky element', function() {
      expect($sticky.parent()).not.toHaveClass('sticky-wrapper')
    })

    it('does not unwrap on destroy', function() {
      var parent = waypoint.wrapper
      waypoint.destroy()
      expect(parent).toBe(waypoint.wrapper)
    })
  })
})
