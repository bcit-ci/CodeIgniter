'use strict'
/* global
 * describe, it, beforeEach, afterEach, expect, spyOn, runs,
 * loadFixtures, Waypoint
 */

window.jQuery.each(Waypoint.adapters, function(i, adapter) {
  describe(adapter.name + ' adapter:', function() {
    describe('Waypoint.Context', function() {
      var $ = window.jQuery
      var currentDirection, $scroller, waypoint, $target, context, skipDestroy

      function setDirection(direction) {
        currentDirection = direction
      }

      beforeEach(function() {
        Waypoint.Adapter = adapter.Adapter
        loadFixtures('standard.html')
        $scroller = $(window)
        currentDirection = null
        skipDestroy = false
        $target = $('#same1')
        waypoint = new Waypoint({
          element: $target[0],
          handler: setDirection
        })
        context = waypoint.context
      })

      afterEach(function() {
        if (!skipDestroy) {
          waypoint.destroy()
        }
        $scroller.scrollTop(0).scrollLeft(0)
      })

      describe('#refresh', function() {
        it('updates trigger point of waypoints', function() {
          var top = $target.offset().top
          $target.css({
            top: top + 1
          })
          context.refresh()
          expect(waypoint.triggerPoint).toEqual(top + 1)
        })

        it('triggers down direction if waypoint crosses upwards', function() {
          $target.css('top', '-1px')
          context.refresh()
          expect(currentDirection).toEqual('down')
        })

        it('triggers up direction if waypoint crosses downwards', function() {
          $target.css('top', '-1px')
          context.refresh()
          $target.css('top', '0px')
          context.refresh()
          expect(currentDirection).toEqual('up')
        })

        it('returns the same context instance for chaining', function() {
          expect(context.refresh()).toEqual(context)
        })
      })

      describe('#destroy', function() {
        it('prevents further waypoint triggers', function() {
          skipDestroy = true
          context.destroy()
          $scroller.scrollTop($target.offset().top)
          expect(currentDirection).toBeNull()
        })
      })

      describe('Waypoint.Context.refreshAll()', function() {
        it('calls refresh on all contexts', function() {
          var secondWaypoint = new Waypoint({
            element: $('#inner3')[0],
            context: $('#bottom')[0],
            handler: function() {}
          })
          var secondContext = secondWaypoint.context
          spyOn(context, 'refresh')
          spyOn(secondContext, 'refresh')
          Waypoint.Context.refreshAll()
          expect(context.refresh).toHaveBeenCalled()
          expect(secondContext.refresh).toHaveBeenCalled()
          secondWaypoint.destroy()
        })
      })
    })
  })
})
