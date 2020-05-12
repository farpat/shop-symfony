import Rule from '../Rule'

export default class LengthRule extends Rule {
  constructor (parameter) {
    if (parameter.min === undefined && parameter.max === undefined) {
      throw 'The << LengthRule >> rule waits in parameter either << min >> parameter or << max >> parameter or both'
    }

    super(parameter)
    this.name = 'length'

    if (this.isDefined(this.parameter.min)) {
      if (parameter.minMessage === 'undefined') {
        throw 'The << LengthRule >> rule waits << minMessage >> because << min >> is filled'
      }
      this.parameter.min = Number.parseInt(this.parameter.min)
    }
    if (this.isDefined(this.parameter.max)) {
      if (parameter.maxMessage === 'undefined') {
        throw 'The << LengthRule >> rule waits << maxMessage >> because << max >> is filled'
      }
      this.parameter.max = Number.parseInt(this.parameter.max)
    }
  }

  isDefined (parameter) {
    return parameter !== undefined && parameter !== ''
  }

  check (value) {
    if (this.isDefined(this.parameter.min) && value.length < this.parameter.min) {
      return this.parameter.minMessage.replace(/%limit%/, this.parameter.min)
    }

    if (this.isDefined(this.parameter.max) && value.length > this.parameter.max) {
      return this.parameter.maxMessage.replace(/%limit%/, this.parameter.max)
    }

    return ''
  }
}
