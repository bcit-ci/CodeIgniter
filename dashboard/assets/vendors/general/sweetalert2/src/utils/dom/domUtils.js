import { swalClasses } from '../classes'

// Remember state in cases where opening and handling a modal will fiddle with it.
export const states = {
  previousBodyPadding: null
}

export const hasClass = (elem, className) => {
  return elem.classList.contains(className)
}

export const focusInput = (input) => {
  input.focus()

  // place cursor at end of text in text input
  if (input.type !== 'file') {
    // http://stackoverflow.com/a/2345915
    const val = input.value
    input.value = ''
    input.value = val
  }
}

const addOrRemoveClass = (target, classList, add) => {
  if (!target || !classList) {
    return
  }
  if (typeof classList === 'string') {
    classList = classList.split(/\s+/).filter(Boolean)
  }
  classList.forEach((className) => {
    if (target.forEach) {
      target.forEach((elem) => {
        add ? elem.classList.add(className) : elem.classList.remove(className)
      })
    } else {
      add ? target.classList.add(className) : target.classList.remove(className)
    }
  })
}

export const addClass = (target, classList) => {
  addOrRemoveClass(target, classList, true)
}

export const removeClass = (target, classList) => {
  addOrRemoveClass(target, classList, false)
}

export const getChildByClass = (elem, className) => {
  for (let i = 0; i < elem.childNodes.length; i++) {
    if (hasClass(elem.childNodes[i], className)) {
      return elem.childNodes[i]
    }
  }
}

export const show = (elem) => {
  elem.style.opacity = ''
  elem.style.display = (elem.id === swalClasses.content) ? 'block' : 'flex'
}

export const hide = (elem) => {
  elem.style.opacity = ''
  elem.style.display = 'none'
}

// borrowed from jquery $(elem).is(':visible') implementation
export const isVisible = (elem) => elem && (elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length)

export const contains = (haystack, needle) => {
  if (typeof haystack.contains === 'function') {
    return haystack.contains(needle)
  }
}
