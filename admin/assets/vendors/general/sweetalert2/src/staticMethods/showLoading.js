import * as dom from '../utils/dom/index'
import sweetAlert from '../sweetalert2'
import { swalClasses } from '../utils/classes'

/**
 * Show spinner instead of Confirm button and disable Cancel button
 */
const showLoading = () => {
  let popup = dom.getPopup()
  if (!popup) {
    sweetAlert('')
  }
  popup = dom.getPopup()
  const actions = dom.getActions()
  const confirmButton = dom.getConfirmButton()
  const cancelButton = dom.getCancelButton()

  dom.show(actions)
  dom.show(confirmButton)
  dom.addClass([popup, actions], swalClasses.loading)
  confirmButton.disabled = true
  cancelButton.disabled = true

  popup.setAttribute('data-loading', true)
  popup.setAttribute('aria-busy', true)
  popup.focus()
}

export {
  showLoading,
  showLoading as enableLoading
}
