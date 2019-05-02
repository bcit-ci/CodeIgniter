import { getContainer } from './dom/getters'
import { contains } from './dom/domUtils'
import { toArray } from './utils'

// From https://developer.paciellogroup.com/blog/2018/06/the-current-state-of-modal-dialog-accessibility/
// Adding aria-hidden="true" to elements outside of the active modal dialog ensures that
// elements not within the active modal dialog will not be surfaced if a user opens a screen
// reader’s list of elements (headings, form controls, landmarks, etc.) in the document.

export const setAriaHidden = () => {
  const bodyChildren = toArray(document.body.children)
  bodyChildren.forEach(el => {
    if (el === getContainer() || contains(el, getContainer())) {
      return
    }

    if (el.hasAttribute('aria-hidden') ) {
      el.setAttribute('data-previous-aria-hidden', el.getAttribute('aria-hidden'))
    }
    el.setAttribute('aria-hidden', 'true')
  })
}

export const unsetAriaHidden = () => {
  const bodyChildren = toArray(document.body.children)
  bodyChildren.forEach(el => {
    if (el.hasAttribute('data-previous-aria-hidden') ) {
      el.setAttribute('aria-hidden', el.getAttribute('data-previous-aria-hidden'))
      el.removeAttribute('data-previous-aria-hidden')
    } else {
      el.removeAttribute('aria-hidden')
    }
  })
}
