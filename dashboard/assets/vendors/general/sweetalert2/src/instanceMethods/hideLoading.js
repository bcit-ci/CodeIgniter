import * as dom from '../utils/dom/index'
import { swalClasses } from '../utils/classes'
import privateProps from '../privateProps'

/**
 * Enables buttons and hide loader.
 */
function hideLoading () {
  const innerParams = privateProps.innerParams.get(this)
  const domCache = privateProps.domCache.get(this)
  if (!innerParams.showConfirmButton) {
    dom.hide(domCache.confirmButton)
    if (!innerParams.showCancelButton) {
      dom.hide(domCache.actions)
    }
  }
  dom.removeClass([domCache.popup, domCache.actions], swalClasses.loading)
  domCache.popup.removeAttribute('aria-busy')
  domCache.popup.removeAttribute('data-loading')
  domCache.confirmButton.disabled = false
  domCache.cancelButton.disabled = false
}

export {
  hideLoading,
  hideLoading as disableLoading
}
