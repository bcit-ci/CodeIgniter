// WeakMap polyfill, needed for Android 4.4
// Related issue: https://github.com/sweetalert2/sweetalert2/issues/1071
// http://webreflection.blogspot.fi/2015/04/a-weakmap-polyfill-in-20-lines-of-code.html

import Symbol from './Symbol'

/* istanbul ignore next */
export default typeof WeakMap === 'function' ? WeakMap : ((s, dP, hOP) => {
  function WeakMap () {
    dP(this, s, { value: Symbol('WeakMap') })
  }
  WeakMap.prototype = {
    'delete': function del (o) {
      delete o[this[s]]
    },
    get: function get (o) {
      return o[this[s]]
    },
    has: function has (o) {
      return hOP.call(o, this[s])
    },
    set: function set (o, v) {
      dP(o, this[s], { configurable: true, value: v })
    }
  }
  return WeakMap
})(Symbol('WeakMap'), Object.defineProperty, {}.hasOwnProperty)
