import { swalClasses, iconTypes } from '../classes'
import { getContainer, getPopup, getContent } from './getters'
import { addClass, removeClass, getChildByClass } from './domUtils'
import { isNodeEnv } from '../isNodeEnv'
import { error } from '../utils'
import sweetAlert from '../../sweetalert2'

const sweetHTML = `
 <div aria-labelledby="${swalClasses.title}" aria-describedby="${swalClasses.content}" class="${swalClasses.popup}" tabindex="-1">
   <div class="${swalClasses.header}">
     <ul class="${swalClasses.progresssteps}"></ul>
     <div class="${swalClasses.icon} ${iconTypes.error}">
       <span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span>
     </div>
     <div class="${swalClasses.icon} ${iconTypes.question}">
       <span class="${swalClasses['icon-text']}">?</span>
      </div>
     <div class="${swalClasses.icon} ${iconTypes.warning}">
       <span class="${swalClasses['icon-text']}">!</span>
      </div>
     <div class="${swalClasses.icon} ${iconTypes.info}">
       <span class="${swalClasses['icon-text']}">i</span>
      </div>
     <div class="${swalClasses.icon} ${iconTypes.success}">
       <div class="swal2-success-circular-line-left"></div>
       <span class="swal2-success-line-tip"></span> <span class="swal2-success-line-long"></span>
       <div class="swal2-success-ring"></div> <div class="swal2-success-fix"></div>
       <div class="swal2-success-circular-line-right"></div>
     </div>
     <img class="${swalClasses.image}" />
     <h2 class="${swalClasses.title}" id="${swalClasses.title}"></h2>
     <button type="button" class="${swalClasses.close}">×</button>
   </div>
   <div class="${swalClasses.content}">
     <div id="${swalClasses.content}"></div>
     <input class="${swalClasses.input}" />
     <input type="file" class="${swalClasses.file}" />
     <div class="${swalClasses.range}">
       <input type="range" />
       <output></output>
     </div>
     <select class="${swalClasses.select}"></select>
     <div class="${swalClasses.radio}"></div>
     <label for="${swalClasses.checkbox}" class="${swalClasses.checkbox}">
       <input type="checkbox" />
       <span class="${swalClasses.label}"></span>
     </label>
     <textarea class="${swalClasses.textarea}"></textarea>
     <div class="${swalClasses['validation-message']}" id="${swalClasses['validation-message']}"></div>
   </div>
   <div class="${swalClasses.actions}">
     <button type="button" class="${swalClasses.confirm}">OK</button>
     <button type="button" class="${swalClasses.cancel}">Cancel</button>
   </div>
   <div class="${swalClasses.footer}">
   </div>
 </div>
`.replace(/(^|\n)\s*/g, '')

/*
 * Add modal + backdrop to DOM
 */
export const init = (params) => {
  // Clean up the old popup if it exists
  const c = getContainer()
  if (c) {
    c.parentNode.removeChild(c)
    removeClass(
      [document.documentElement, document.body],
      [
        swalClasses['no-backdrop'],
        swalClasses['toast-shown'],
        swalClasses['has-column']
      ]
    )
  }

  /* istanbul ignore if */
  if (isNodeEnv()) {
    error('SweetAlert2 requires document to initialize')
    return
  }

  const container = document.createElement('div')
  container.className = swalClasses.container
  container.innerHTML = sweetHTML

  let targetElement = typeof params.target === 'string' ? document.querySelector(params.target) : params.target
  targetElement.appendChild(container)

  const popup = getPopup()
  const content = getContent()
  const input = getChildByClass(content, swalClasses.input)
  const file = getChildByClass(content, swalClasses.file)
  const range = content.querySelector(`.${swalClasses.range} input`)
  const rangeOutput = content.querySelector(`.${swalClasses.range} output`)
  const select = getChildByClass(content, swalClasses.select)
  const checkbox = content.querySelector(`.${swalClasses.checkbox} input`)
  const textarea = getChildByClass(content, swalClasses.textarea)

  // a11y
  popup.setAttribute('role', params.toast ? 'alert' : 'dialog')
  popup.setAttribute('aria-live', params.toast ? 'polite' : 'assertive')
  if (!params.toast) {
    popup.setAttribute('aria-modal', 'true')
  }

  // RTL
  if (window.getComputedStyle(targetElement).direction === 'rtl') {
    addClass(getContainer(), swalClasses.rtl)
  }

  let oldInputVal // IE11 workaround, see #1109 for details
  const resetValidationMessage = (e) => {
    if (sweetAlert.isVisible() && oldInputVal !== e.target.value) {
      sweetAlert.resetValidationMessage()
    }
    oldInputVal = e.target.value
  }

  input.oninput = resetValidationMessage
  file.onchange = resetValidationMessage
  select.onchange = resetValidationMessage
  checkbox.onchange = resetValidationMessage
  textarea.oninput = resetValidationMessage

  range.oninput = (e) => {
    resetValidationMessage(e)
    rangeOutput.value = range.value
  }

  range.onchange = (e) => {
    resetValidationMessage(e)
    range.nextSibling.value = range.value
  }

  return popup
}
