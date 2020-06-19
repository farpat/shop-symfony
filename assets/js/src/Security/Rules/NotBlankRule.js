import Rule from '../Rule'

export default class NotBlankRule extends Rule {
  constructor (parameter) {
    super(parameter)
    this.name = 'not-blank'
  }

  check (value) {
    return value.length > 0 ? '' : this.parameter.message
  }
}
