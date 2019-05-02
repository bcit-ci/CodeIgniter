(function() {
  'use strict'

  var $ = window.Zepto
  var Waypoint = window.Waypoint

  function ZeptoAdapter(element) {
    this.element = element
    this.$element = $(element)
  }

  $.each([
    'off',
    'on',
    'scrollLeft',
    'scrollTop'
  ], function(i, method) {
    ZeptoAdapter.prototype[method] = function() {
      var args = Array.prototype.slice.call(arguments)
      return this.$element[method].apply(this.$element, args)
    }
  })

  ZeptoAdapter.prototype.offset = function() {
    if (this.element !== this.element.window) {
      return this.$element.offset()
    }
  }

  // Adapted from https://gist.github.com/wheresrhys/5823198
  $.each([
    'width',
    'height'
  ], function(i, dimension) {
    function createDimensionMethod($element, includeBorder) {
      return function(includeMargin) {
        var $element = this.$element
        var size = $element[dimension]()
        var sides = {
          width: ['left', 'right'],
          height: ['top', 'bottom']
        }

        $.each(sides[dimension], function(i, side) {
          size += parseInt($element.css('padding-' + side), 10)
          if (includeBorder) {
            size += parseInt($element.css('border-' + side + '-width'), 10)
          }
          if (includeMargin) {
            size += parseInt($element.css('margin-' + side), 10)
          }
        })
        return size
      }
    }

    var innerMethod = $.camelCase('inner-' + dimension)
    var outerMethod = $.camelCase('outer-' + dimension)

    ZeptoAdapter.prototype[innerMethod] = createDimensionMethod(false)
    ZeptoAdapter.prototype[outerMethod] = createDimensionMethod(true)
  })

  $.each([
    'extend',
    'inArray'
  ], function(i, method) {
    ZeptoAdapter[method] = $[method]
  })

  ZeptoAdapter.isEmptyObject = function(obj) {
    /* eslint no-unused-vars: 0 */
    for (var name in obj) {
      return false
    }
    return true
  }

  Waypoint.adapters.push({
    name: 'zepto',
    Adapter: ZeptoAdapter
  })
  Waypoint.Adapter = ZeptoAdapter
}())
