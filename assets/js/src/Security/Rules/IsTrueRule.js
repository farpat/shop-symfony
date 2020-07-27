import Rule from '../Rule'

export default class IsTrueRule extends Rule {
  constructor (parameter) {
    super(parameter)
    this.name = 'is-true'
  }

  check (value) {
    return value ? undefined : this.parameter.message
  }
}
