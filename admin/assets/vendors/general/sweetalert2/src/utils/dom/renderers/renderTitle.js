import * as dom from '../../dom/index'

export const renderTitle = (params) => {
  const title = dom.getTitle()

  if (params.titleText) {
    title.innerText = params.titleText
  } else if (params.title) {
    if (typeof params.title === 'string') {
      params.title = params.title.split('\n').join('<br />')
    }
    dom.parseHtmlToContainer(params.title, title)
  }
}
