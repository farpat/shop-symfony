import Rule from '../Rule'

export default class EmailRule extends Rule {
  constructor (parameter) {
    super(parameter)
    this.name = 'email'
  }

  check (value) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    return re.test(String(value).toLowerCase()) ? undefined : this.parameter.message
  }
}
