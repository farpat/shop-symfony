export default class NumericRule {
    constructor() {
        this.name = 'numeric';
    }

    check(value) {
        return (
            (value === '' || value === undefined) ||
            (!isNaN(value) && typeof value === 'number')
        );
    }
}
