import { swalClasses } from '../../classes.js'
import * as dom from '../../dom/index'

export const renderActions = (params) => {
  const actions = dom.getActions()
  const confirmButton = dom.getConfirmButton()
  const cancelButton = dom.getCancelButton()

  // Actions (buttons) wrapper
  if (!params.showConfirmButton && !params.showCancelButton) {
    dom.hide(actions)
  } else {
    dom.show(actions)
  }

  // Cancel button
  if (params.showCancelButton) {
    cancelButton.style.display = 'inline-block'
  } else {
    dom.hide(cancelButton)
  }

  // Confirm button
  if (params.showConfirmButton) {
    confirmButton.style.removeProperty('display')
  } else {
    dom.hide(confirmButton)
  }

  // Edit text on confirm and cancel buttons
  confirmButton.innerHTML = params.confirmButtonText
  cancelButton.innerHTML = params.cancelButtonText

  // ARIA labels for confirm and cancel buttons
  confirmButton.setAttribute('aria-label', params.confirmButtonAriaLabel)
  cancelButton.setAttribute('aria-label', params.cancelButtonAriaLabel)

  // Add buttons custom classes
  confirmButton.className = swalClasses.confirm
  dom.addClass(confirmButton, params.confirmButtonClass)
  cancelButton.className = swalClasses.cancel
  dom.addClass(cancelButton, params.cancelButtonClass)

  // Buttons styling
  if (params.buttonsStyling) {
    dom.addClass([confirmButton, cancelButton], swalClasses.styled)

    // Buttons background colors
    if (params.confirmButtonColor) {
      confirmButton.style.backgroundColor = params.confirmButtonColor
    }
    if (params.cancelButtonColor) {
      cancelButton.style.backgroundColor = params.cancelButtonColor
    }

    // Loading state
    const confirmButtonBackgroundColor = window.getComputedStyle(confirmButton).getPropertyValue('background-color')
    confirmButton.style.borderLeftColor = confirmButtonBackgroundColor
    confirmButton.style.borderRightColor = confirmButtonBackgroundColor
  } else {
    dom.removeClass([confirmButton, cancelButton], swalClasses.styled)

    confirmButton.style.backgroundColor = confirmButton.style.borderLeftColor = confirmButton.style.borderRightColor = ''
    cancelButton.style.backgroundColor = cancelButton.style.borderLeftColor = cancelButton.style.borderRightColor = ''
  }
}
