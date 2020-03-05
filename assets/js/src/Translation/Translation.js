import Arr from "../Array/Arr";

const getMainTranslation = function (key) {
    this.loadMainTranslation();
    return this.translations[`${this.lang}.json`][key];
};

const getTranslation = function (key) {
    const regex = /([a-z_-]+\.)+([a-z_-]+)/g;
    if (regex.test(key)) {
        const keys = key.split('.');
        this.loadTranslation(keys[0]);
        return Arr.getNestedProperty(this.translations[this.lang], keys);
    }

    return undefined;
};

class Translation {
    constructor() {
        this.lang = document.querySelector('html').getAttribute('lang') || 'en';
        this.translations = {};
    }

    loadMainTranslation() {
        let json;

        if (this.translations[`${this.lang}.json`] === undefined) {
            try {
                json = require(`../../../js-lang/${this.lang}.json`);
            } catch (e) {
                this.lang = 'en';

                try {
                    json = require(`../../../js-lang/${this.lang}.json`);
                } catch (e) {
                    json = {};
                }
            }

            this.translations[`${this.lang}.json`] = json;
        }
    }

    loadTranslation(key) {
        let json;

        if (this.translations[this.lang] === undefined) {
            this.translations[this.lang] = {};
        }

        if (this.translations[this.lang][key] === undefined) {
            try {
                json = require(`../../../js-lang/${this.lang}/${key}.json`);
            } catch (e) {
                this.lang = 'en';

                try {
                    json = require(`../../../js-lang/${this.lang}/${key}.json`);
                } catch (e) {
                    json = {};
                }
            }
            this.translations[this.lang][key] = json;
        }
    }

    get(key) {
        return getMainTranslation.call(this, key) || getTranslation.call(this, key) || key;
    }
}

export default new Translation();
