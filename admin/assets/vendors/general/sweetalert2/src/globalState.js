import { RESTORE_FOCUS_TIMEOUT } from './constants'

const globalState = {}

export default globalState

// Restore previous active (focused) element
export const restoreActiveElement = () => {
  return new Promise(resolve => {
    const x = window.scrollX
    const y = window.scrollY
    globalState.restoreFocusTimeout = setTimeout(() => {
      if (globalState.previousActiveElement && globalState.previousActiveElement.focus) {
        globalState.previousActiveElement.focus()
        globalState.previousActiveElement = null
      } else if (document.body) {
        document.body.focus()
      }
      resolve()
    }, RESTORE_FOCUS_TIMEOUT) // issues/900
    if (typeof x !== 'undefined' && typeof y !== 'undefined') { // IE doesn't have scrollX/scrollY support
      window.scrollTo(x, y)
    }
  })
}
