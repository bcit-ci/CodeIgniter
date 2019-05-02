// https://github.com/Riim/symbol-polyfill/blob/master/index.js

/* istanbul ignore next */
export default typeof Symbol === 'function' ? Symbol : (() => {
  let idCounter = 0
  function Symbol (key) {
    return '__' + key + '_' + Math.floor(Math.random() * 1e9) + '_' + (++idCounter) + '__'
  }
  Symbol.iterator = Symbol('Symbol.iterator')
  return Symbol
})()
