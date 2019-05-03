// Measure width of scrollbar
// https://github.com/twbs/bootstrap/blob/master/js/modal.js#L279-L286
export const measureScrollbar = () => {
  const supportsTouch = 'ontouchstart' in window || navigator.msMaxTouchPoints
  if (supportsTouch) {
    return 0
  }
  const scrollDiv = document.createElement('div')
  scrollDiv.style.width = '50px'
  scrollDiv.style.height = '50px'
  scrollDiv.style.overflow = 'scroll'
  document.body.appendChild(scrollDiv)
  const scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
  document.body.removeChild(scrollDiv)
  return scrollbarWidth
}
