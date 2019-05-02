import { swalClasses } from '../../classes.js'
import * as dom from '../../dom/index'

export const renderContent = (params) => {
  const content = dom.getContent().querySelector('#' + swalClasses.content)

  // Content as HTML
  if (params.html) {
    dom.parseHtmlToContainer(params.html, content)

    // Content as plain text
  } else if (params.text) {
    content.textContent = params.text
    dom.show(content)
  } else {
    dom.hide(content)
  }
}
