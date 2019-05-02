import * as dom from '../utils/dom/index'
import { warnOnce } from '../utils/utils'
import { swalClasses } from '../utils/classes'
import privateProps from '../privateProps'

// Show block with validation message
export function showValidationMessage (error) {
  const domCache = privateProps.domCache.get(this)
  domCache.validationMessage.innerHTML = error
  const popupComputedStyle = window.getComputedStyle(domCache.popup)
  domCache.validationMessage.style.marginLeft = `-${popupComputedStyle.getPropertyValue('padding-left')}`
  domCache.validationMessage.style.marginRight = `-${popupComputedStyle.getPropertyValue('padding-right')}`
  dom.show(domCache.validationMessage)

  const input = this.getInput()
  if (input) {
    input.setAttribute('aria-invalid', true)
    input.setAttribute('aria-describedBy', swalClasses['validation-message'])
    dom.focusInput(input)
    dom.addClass(input, swalClasses.inputerror)
  }
}

// Hide block with validation message
export function resetValidationMessage () {
  const domCache = privateProps.domCache.get(this)
  if (domCache.validationMessage) {
    dom.hide(domCache.validationMessage)
  }

  const input = this.getInput()
  if (input) {
    input.removeAttribute('aria-invalid')
    input.removeAttribute('aria-describedBy')
    dom.removeClass(input, swalClasses.inputerror)
  }
}

// @deprecated
/* istanbul ignore next */
export function resetValidationError() {
  warnOnce(`Swal.resetValidationError() is deprecated and will be removed in the next major release, use Swal.resetValidationMessage() instead`)
  resetValidationMessage.bind(this)()
}

// @deprecated
/* istanbul ignore next */
export function showValidationError(error) {
  warnOnce(`Swal.showValidationError() is deprecated and will be removed in the next major release, use Swal.showValidationMessage() instead`)
  showValidationMessage.bind(this)(error)
}
