export default class LengthRule {
    constructor(parameter) {
        if (parameter.min === undefined && parameter.max === undefined) {
            throw "The LengthRule rule waits in parameter either min parameter or max parameter or both";
        }

        this.name = 'length';

        this.parameter = parameter;
        if (this.isDefined(this.parameter.min)) {
            this.parameter.min = Number.parseInt(this.parameter.min);
        }
        if (this.isDefined(this.parameter.max)) {
            this.parameter.max = Number.parseInt(this.parameter.max);
        }
    }

    isDefined(parameter) {
        return parameter !== undefined && parameter !== '';
    }

    check(value) {
        if (this.isDefined(this.parameter.min) && value.length < this.parameter.min) {
            return false;
        }

        if (this.isDefined(this.parameter.max) && value.length > this.parameter.max) {
            return false;
        }

        return true;
    }
}
