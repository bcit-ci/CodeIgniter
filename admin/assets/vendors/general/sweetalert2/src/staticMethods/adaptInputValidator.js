/**
 * Adapt a legacy inputValidator for use with expectRejections=false
 */
export const adaptInputValidator = (legacyValidator) => {
  return function adaptedInputValidator (inputValue, extraParams) {
    return legacyValidator.call(this, inputValue, extraParams)
      .then(() => undefined, validationMessage => validationMessage)
  }
}
