export default {
  email: (string, extraParams) => {
    return /^[a-zA-Z0-9.+_-]+@[a-zA-Z0-9.-]+\.[a-zA-Z0-9-]{2,24}$/.test(string)
      ? Promise.resolve()
      : Promise.reject(extraParams && extraParams.validationMessage ? extraParams.validationMessage : 'Invalid email address')
  },
  url: (string, extraParams) => {
    // taken from https://stackoverflow.com/a/3809435 with a small change from #1306
    return /^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._+~#=]{2,256}\.[a-z]{2,63}\b([-a-zA-Z0-9@:%_+.~#?&//=]*)$/.test(string)
      ? Promise.resolve()
      : Promise.reject(extraParams && extraParams.validationMessage ? extraParams.validationMessage : 'Invalid URL')
  }
}
