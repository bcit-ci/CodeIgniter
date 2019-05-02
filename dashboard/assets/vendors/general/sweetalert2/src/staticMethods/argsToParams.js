import { error } from '../utils/utils.js'

export const argsToParams = (args) => {
  const params = {}
  switch (typeof args[0]) {
    case 'object':
      Object.assign(params, args[0])
      break

    default:
      ['title', 'html', 'type'].forEach((name, index) => {
        switch (typeof args[index]) {
          case 'string':
            params[name] = args[index]
            break
          case 'undefined':
            break
          default:
            error(`Unexpected type of ${name}! Expected "string", got ${typeof args[index]}`)
        }
      })
  }
  return params
}
