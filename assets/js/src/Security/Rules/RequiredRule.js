export default class RequiredRule {
    constructor() {
        this.name = 'required';
    }

    check(value) {
        if (value === null || value === undefined || value === '') {
            return false;
        }

        const typeOfValue = typeof value;

        if (typeOfValue === 'object' || typeOfValue === 'string') {
            return value.length > 0;
        } else if (typeOfValue === 'boolean') {
            return value;
        }
    }
}
