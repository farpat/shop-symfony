export default class RequiredRule {
    constructor() {
        this.name = 'required';
    }

    check(value) {
        if (value === null || value === undefined || value === '') {
            return false;
        }

        if (typeof value === 'object' || typeof value === 'string') {
            return value.length > 0;
        } else if (typeof value === 'boolean') {
            return value;
        }
    }
}
