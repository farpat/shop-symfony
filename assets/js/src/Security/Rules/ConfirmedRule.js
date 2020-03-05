export default class ConfirmedRule {
    constructor(params) {
        this.selector = params;
        this.name = 'confirmed';
    }

    check(value) {
        if (this.confirmedElement === undefined) {
            this.confirmedElement = document.querySelector(this.selector);
            if (!(this.confirmedElement instanceof HTMLElement)) {
                throw `${params} is a bad css selector`;
            }
        }

        return (this.confirmedElement.value === '' || value === this.confirmedElement.value);
    }
}
