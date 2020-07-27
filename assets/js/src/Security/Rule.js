export default class Rule {
  constructor (parameter) {
    const className = this.constructor.name

    if (className !== 'LengthRule' && parameter.message === undefined) {
      //LengthRule must contains parameter.minMessage and parameter.maxMessage
      //The parameters control is done in LengthRule constructor
      throw `You must fill << message >> in << ${className} >>. It's used for the error message displaying.`
    }

    this.parameter = parameter
  }

  check (value) {
    const className = this.constructor.name
    throw `You must implement << check(string value): ?string >> method in << ${className} >>`
  }

  /**
   *
   * @param {Array} rulesInArray
   */
  static makeRulesInString (rulesInArray) {
    return rulesInArray.reduce((acc, rule) => {
      const prefix = acc === '' ? '' : '²'
      return `${acc}${prefix}${rule.rule}ßmessage:${rule.message}`
    }, '')
  }
}
