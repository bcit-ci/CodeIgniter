import * as dom from '../utils/dom/index'
import privateProps from '../privateProps'

export function getProgressSteps () {
  const innerParams = privateProps.innerParams.get(this)
  return innerParams.progressSteps
}

export function setProgressSteps (progressSteps) {
  const innerParams = privateProps.innerParams.get(this)
  const updatedParams = Object.assign({}, innerParams, { progressSteps })
  privateProps.innerParams.set(this, updatedParams)
  dom.renderProgressSteps(updatedParams)
}

export function showProgressSteps () {
  const domCache = privateProps.domCache.get(this)
  dom.show(domCache.progressSteps)
}

export function hideProgressSteps () {
  const domCache = privateProps.domCache.get(this)
  dom.hide(domCache.progressSteps)
}
