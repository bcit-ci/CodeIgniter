import { error } from './utils/utils.js'
import { DismissReason } from './utils/DismissReason'
import * as staticMethods from './staticMethods'
import * as instanceMethods from './instanceMethods'
import privateProps from './privateProps'

let currentInstance

// SweetAlert constructor
function SweetAlert (...args) {
  // Prevent run in Node env
  /* istanbul ignore if */
  if (typeof window === 'undefined') {
    return
  }

  // Check for the existence of Promise
  /* istanbul ignore if */
  if (typeof Promise === 'undefined') {
    error('This package requires a Promise library, please include a shim to enable it in this browser (See: https://github.com/sweetalert2/sweetalert2/wiki/Migration-from-SweetAlert-to-SweetAlert2#1-ie-support)')
  }

  currentInstance = this

  const outerParams = Object.freeze(this.constructor.argsToParams(args))

  Object.defineProperties(this, {
    params: {
      value: outerParams,
      writable: false,
      enumerable: true
    }
  })

  const promise = this._main(this.params)
  privateProps.promise.set(this, promise)
}

// `catch` cannot be the name of a module export, so we define our thenable methods here instead
SweetAlert.prototype.then = function (onFulfilled, onRejected) {
  const promise = privateProps.promise.get(this)
  return promise.then(onFulfilled, onRejected)
}
SweetAlert.prototype.catch = function (onRejected) {
  const promise = privateProps.promise.get(this)
  return promise.catch(onRejected)
}
SweetAlert.prototype.finally = function (onFinally) {
  const promise = privateProps.promise.get(this)
  return promise.finally(onFinally)
}

// Assign instance methods from src/instanceMethods/*.js to prototype
Object.assign(SweetAlert.prototype, instanceMethods)

// Assign static methods from src/staticMethods/*.js to constructor
Object.assign(SweetAlert, staticMethods)

// Proxy to instance methods to constructor, for now, for backwards compatibility
Object.keys(instanceMethods).forEach(key => {
  SweetAlert[key] = function (...args) {
    if (currentInstance) {
      return currentInstance[key](...args)
    }
  }
})

SweetAlert.DismissReason = DismissReason

/* istanbul ignore next */
SweetAlert.noop = () => { }

export default SweetAlert
