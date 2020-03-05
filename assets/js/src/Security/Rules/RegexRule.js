export default class RegexRule {
    constructor(regex) {
        this.params = regex
        this.name = 'regex';
    }

    check(value) {
        return this.params.test(value);
    }
}
