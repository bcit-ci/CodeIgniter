import { warnOnce } from '../utils/utils'
import { showWarningsForParams } from '../utils/params'

const deprecationWarning = `\
"setDefaults" & "resetDefaults" methods are deprecated in favor of "mixin" method and will be removed in the next major release. \
For new projects, use "mixin". For past projects already using "setDefaults", support will be provided through an additional package.`
let defaults = {}

export function withGlobalDefaults (ParentSwal) {
  class SwalWithGlobalDefaults extends ParentSwal {
    _main (params) {
      return super._main(Object.assign({}, defaults, params))
    }

    static setDefaults (params) {
      warnOnce(deprecationWarning)
      if (!params || typeof params !== 'object') {
        throw new TypeError('SweetAlert2: The argument for setDefaults() is required and has to be a object')
      }
      showWarningsForParams(params)
      // assign valid params from `params` to `defaults`
      Object.keys(params).forEach(param => {
        if (ParentSwal.isValidParameter(param)) {
          defaults[param] = params[param]
        }
      })
    }

    static resetDefaults () {
      warnOnce(deprecationWarning)
      defaults = {}
    }
  }

  // Set default params if `window._swalDefaults` is an object
  if (typeof window !== 'undefined' && typeof window._swalDefaults === 'object') {
    SwalWithGlobalDefaults.setDefaults(window._swalDefaults)
  }

  return SwalWithGlobalDefaults
}
