import { swalClasses } from '../classes'
import { uniqueArray, warnOnce, toArray } from '../utils'
import { isVisible } from './domUtils'

export const getContainer = () => document.body.querySelector('.' + swalClasses.container)

const elementByClass = (className) => {
  const container = getContainer()
  return container ? container.querySelector('.' + className) : null
}

export const getPopup = () => elementByClass(swalClasses.popup)

export const getIcons = () => {
  const popup = getPopup()
  return toArray(popup.querySelectorAll('.' + swalClasses.icon))
}

export const getTitle = () => elementByClass(swalClasses.title)

export const getContent = () => elementByClass(swalClasses.content)

export const getImage = () => elementByClass(swalClasses.image)

export const getProgressSteps = () => elementByClass(swalClasses.progresssteps)

export const getValidationMessage = () => elementByClass(swalClasses['validation-message'])

export const getConfirmButton = () => elementByClass(swalClasses.confirm)

export const getCancelButton = () => elementByClass(swalClasses.cancel)

/* @deprecated */
/* istanbul ignore next */
export const getButtonsWrapper = () => {
  warnOnce(`swal.getButtonsWrapper() is deprecated and will be removed in the next major release, use swal.getActions() instead`)
  return elementByClass(swalClasses.actions)
}

export const getActions = () => elementByClass(swalClasses.actions)

export const getFooter = () => elementByClass(swalClasses.footer)

export const getCloseButton = () => elementByClass(swalClasses.close)

export const getFocusableElements = () => {
  const focusableElementsWithTabindex = toArray(
    getPopup().querySelectorAll('[tabindex]:not([tabindex="-1"]):not([tabindex="0"])')
  )
  // sort according to tabindex
    .sort((a, b) => {
      a = parseInt(a.getAttribute('tabindex'))
      b = parseInt(b.getAttribute('tabindex'))
      if (a > b) {
        return 1
      } else if (a < b) {
        return -1
      }
      return 0
    })

  // https://github.com/jkup/focusable/blob/master/index.js
  const otherFocusableElements = toArray(
    getPopup().querySelectorAll('a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, [tabindex="0"], [contenteditable], audio[controls], video[controls]')
  ).filter(el => el.getAttribute('tabindex') !== '-1')

  return uniqueArray(focusableElementsWithTabindex.concat(otherFocusableElements)).filter(el => isVisible(el))
}

export const isModal = () => {
  return !isToast() && !document.body.classList.contains(swalClasses['no-backdrop'])
}

export const isToast = () => {
  return document.body.classList.contains(swalClasses['toast-shown'])
}

export const isLoading = () => {
  return getPopup().hasAttribute('data-loading')
}
