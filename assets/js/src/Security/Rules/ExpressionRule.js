import Rule from '../Rule'

export default class ExpressionRule extends Rule {
  constructor (parameter) {
    super(parameter)
    this.name = 'expression'
  }

  check (value) {
    return eval(this.parameter.expression) ? undefined : this.parameter.message
  }
}
