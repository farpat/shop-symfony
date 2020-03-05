import Vue from "vue";
import Security from "../Security/Security";
import Str from "../String/Str";
import Arr from "../Array/Arr";

/**
 *
 * @param {String} stateKey
 * @param {String} field Field is as "field" or "field[key1]" or "field[key1][key2]" etc.
 * @param {String|Number} value
 */
const set = function (stateKey, field, value) {
    const keys = Str.parseKeysInString(field);

    if (Array.isArray(keys)) {
        const parsedValue = Arr.returnNestedObject(this.state[stateKey], field, value);
        Vue.set(this.state[stateKey], keys[0], {...parsedValue[keys[0]]}); //to force a new reference for object
    } else {
        Vue.set(this.state[stateKey], field, value);
    }
};
/**
 *
 * @param {String} stateKey
 * @param {String} field
 * @returns {*}
 */
const get = function (stateKey, field) {
    if (field === undefined) {
        return this.state[stateKey];
    }

    const keys = Str.parseKeysInString(field);

    return Array.isArray(keys) ?
        Arr.getNestedProperty(this.state[stateKey], keys) :
        this.state[stateKey][field];
};

class Store {
    constructor() {
        const store = window._Store || {};

        this.state = {
            datas:  store.datas || {},
            errors: store.errors || {},
        };

        this.rules = {};
    }

    /**
     *
     * @param {String} field
     * @returns {*}
     */
    getData(field) {
        return get.call(this, 'datas', field);
    }

    /**
     *
     * @param {String} field
     * @returns {*}
     */
    getError(field) {
        return get.call(this, 'errors', field);
    }

    /**
     *
     * @param {Object} object
     * @param {String|Number} key
     * @param {*} value
     */
    set(object, key, value) {
        Vue.set(object, key, value);
    }

    /**
     *
     * @param {String} field
     * @param {*} value
     */
    setData(field, value) {
        set.call(this, 'datas', field, value);
    }

    /**
     *
     * @param {String} field
     * @param {String} value
     */
    setError(field, value) {
        set.call(this, 'errors', field, value);
    }

    /**
     *
     * @param {String} field
     */
    deleteData(field) {
        const keys = Str.parseKeysInString(field);
        if (Array.isArray(keys)) {
            Vue.delete(Arr.getNestedProperty(this.state.datas, keys.slice(0, -1)), keys[keys.length - 1]);
        } else {
            Vue.delete(this.state.datas, field);
        }
    }

    /**
     *
     * @param {String} field
     * @param {String|Number|Array} value
     * @param {Array} rules
     * @param {Boolean} isConfirmationField
     * @returns {string}
     */
    checkData(field, value, rules, isConfirmationField = false) {
        if (!isConfirmationField) {
            const confirmationField = field.slice(-1) === ']' ?
                `${field.slice(0, field.length - 1)}_confirmation]` :
                `${field}_confirmation`;

            const rulesConfirmation = this.getRules(confirmationField);
            if (rulesConfirmation !== undefined) {
                this.checkData(confirmationField, this.getData(confirmationField), rulesConfirmation, true);
            }
        }

        const error = Security.getError(rules, field, value);
        this.setError(field, error);

        return error;
    }

    /**
     *
     * @param {Object} ruleKeys
     */
    checkStore(ruleKeys) {
        if (ruleKeys === undefined) {
            ruleKeys = this.getRuleKeys();
        }

        let checkStore = true;

        ruleKeys.forEach(ruleKey => {
            const rules = this.getRules(ruleKey);
            if (rules.length > 0) {
                const splitedField = ruleKey.split('.');

                if (splitedField.length === 1) {
                    if (!this.checkData(ruleKey, this.getData(ruleKey), rules)) {
                        checkStore = false;
                    }
                } else {
                    const data = this.getData(splitedField[0]);

                    if (Arr.isAssociative(data)) {
                        for (let dataKey in data) {
                            const field = splitedField[2] || splitedField[0]; //addresses 1 text | quantity 2
                            const value = splitedField[2] ? data[dataKey][splitedField[2]] : data[dataKey];

                            if (!this.checkData(field, value, rules)) {
                                checkStore = false;
                            }
                        }
                    } else {
                        console.error('TODO not associative');
                    }
                }
            }
        });

        return checkStore;
    }

    /**
     *
     * @param {String} field
     * @param {Array} rules
     */
    setRules(field, rules) {
        this.rules[field] = rules;
    }

    /**
     *
     * @param {String} field
     * @returns {*}
     */
    getRules(field) {
        return this.rules[field];
    }

    /**
     *
     * @returns {String[]}
     */
    getRuleKeys() {
        return Object.keys(this.rules);
    }
}

export default new Store();
