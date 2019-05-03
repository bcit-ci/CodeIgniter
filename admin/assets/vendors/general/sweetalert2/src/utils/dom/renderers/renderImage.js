import { swalClasses } from '../../classes.js'
import * as dom from '../../dom/index'

export const renderImage = (params) => {
  const image = dom.getImage()

  if (params.imageUrl) {
    image.setAttribute('src', params.imageUrl)
    image.setAttribute('alt', params.imageAlt)
    dom.show(image)

    if (params.imageWidth) {
      image.setAttribute('width', params.imageWidth)
    } else {
      image.removeAttribute('width')
    }

    if (params.imageHeight) {
      image.setAttribute('height', params.imageHeight)
    } else {
      image.removeAttribute('height')
    }

    image.className = swalClasses.image
    if (params.imageClass) {
      dom.addClass(image, params.imageClass)
    }
  } else {
    dom.hide(image)
  }
}
