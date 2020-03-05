export default class MinRule {
    constructor(min, type = 'string') {
        this.type = type;
        this.params = min;
        this.name = 'min';
    }

    check(value) {
        if (this.type === 'string') {
            return (value.length === 0 || value.length > this.params);
        }

        if (this.type === 'number') {
            return value >= this.params;
        }

        return false;
    }
}
