import Translation from "../Translation/Translation";
import Str from "../String/Str";

const getType = function (value) {
    let type = 'file';

    if (typeof value === 'string') {
        type = 'string';
    } else if (isFinite(value)) {
        type = 'numeric';
    }

    return type;
};

const isMinMaxRule = function (type) {
    return type === 'min' || type === 'max';
};

class Security {
    constructor() {
        this.attributes = Translation.get('validation.attributes');
    }

    /**
     *
     * @param {Array} rules Rules of the HTML element
     * @param {String} name Name attribut of the HTML element
     * @param {object} value Value of the HTML element
     * @returns {string}
     */
    getError(rules, name, value) {
        const rulesLength = rules.length;

        for (let i = 0; i < rulesLength; i++) {
            let rule = rules[i];
            if (!rule.check(value)) {
                const parseKeys = Str.parseKeysInString(name);
                name = typeof parseKeys === 'string' ? parseKeys : parseKeys[parseKeys.length - 1];

                let error = Translation.get('validation.' + rule.name);
                const attribute = this.attributes[name] || name;
                if (isMinMaxRule(rule.name)) {
                    const type = getType(value);
                    error = error[type].replace(':attribute', attribute).replace(/:[a-z]*/gi, rule.params);
                } else {
                    error = error.replace(':attribute', attribute);
                }

                return error;
            }
        }

        return undefined;
    }
}

export default new Security();
