import { undoScrollbar } from '../utils/scrollbarFix'
import { undoIOSfix } from '../utils/iosFix'
import { undoIEfix } from '../utils/ieFix'
import { unsetAriaHidden } from '../utils/aria'
import * as dom from '../utils/dom/index'
import { swalClasses } from '../utils/classes.js'
import globalState, { restoreActiveElement } from '../globalState'

/*
 * Global function to close sweetAlert
 */
const close = (onClose, onAfterClose) => {
  const container = dom.getContainer()
  const popup = dom.getPopup()
  if (!popup) {
    return
  }

  if (onClose !== null && typeof onClose === 'function') {
    onClose(popup)
  }

  dom.removeClass(popup, swalClasses.show)
  dom.addClass(popup, swalClasses.hide)

  const removePopupAndResetState = () => {
    if (!dom.isToast()) {
      restoreActiveElement().then(() => triggerOnAfterClose(onAfterClose))
      globalState.keydownTarget.removeEventListener('keydown', globalState.keydownHandler, { capture: globalState.keydownListenerCapture })
      globalState.keydownHandlerAdded = false
    } else {
      triggerOnAfterClose(onAfterClose)
    }

    if (container.parentNode) {
      container.parentNode.removeChild(container)
    }
    dom.removeClass(
      [document.documentElement, document.body],
      [
        swalClasses.shown,
        swalClasses['height-auto'],
        swalClasses['no-backdrop'],
        swalClasses['toast-shown'],
        swalClasses['toast-column']
      ]
    )

    if (dom.isModal()) {
      undoScrollbar()
      undoIOSfix()
      undoIEfix()
      unsetAriaHidden()
    }
  }

  // If animation is supported, animate
  if (dom.animationEndEvent && !dom.hasClass(popup, swalClasses.noanimation)) {
    popup.addEventListener(dom.animationEndEvent, function swalCloseEventFinished () {
      popup.removeEventListener(dom.animationEndEvent, swalCloseEventFinished)
      if (dom.hasClass(popup, swalClasses.hide)) {
        removePopupAndResetState()
      }
    })
  } else {
    // Otherwise, remove immediately
    removePopupAndResetState()
  }
}

const triggerOnAfterClose = (onAfterClose) => {
  if (onAfterClose !== null && typeof onAfterClose === 'function') {
    setTimeout(() => {
      onAfterClose()
    })
  }
}

export {
  close,
  close as closePopup,
  close as closeModal,
  close as closeToast
}
