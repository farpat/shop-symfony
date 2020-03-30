export default class ExpressionRule {
    constructor(parameter) {
        this.name = 'expression';
        this.parameter = parameter;
        this.tracked = false;
    }

    check(value) {
        return eval(this.parameter.expression);
    }
}
