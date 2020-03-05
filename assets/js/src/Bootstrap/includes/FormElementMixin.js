import Store from "../Store";
import Str from "../../String/Str";
import Arr from "../../Array/Arr";

export default {
    props:    {
        id:             {type: String, required: false},
        name:           {type: String, required: true},
        label:          {type: String, default: ''},
        rules:          {type: String, default: ''},
        dataAttributes: {
            type: Object, default: function () {
                return {};
            }
        },
    },
    mounted:  function () {
        this.setRules();
    },
    computed: {
        getDataAttributes: function () {
            let dataAttributes = {};
            if (!Arr.isEmpty(this.dataAttributes)) {
                for (let key in this.dataAttributes) {
                    const realKey = key.startsWith('data-') ? key : 'data-' + key;
                    dataAttributes[realKey] = this.dataAttributes[key];
                }
            }

            return dataAttributes;
        },
        getName:           function () {
            return this.name + (this.multiple ? '[]' : '');
        },
        getId:             function () {
            return this.id || this.name;
        },
        isRequired:        function () {
            this.setRules();

            return !!this.$rules.find(rule => rule.name === 'required');
        },
        getValue:          function () {
            return Store.getData(this.name);
        },
        getError:          function () {
            return Store.getError(this.name) || '';
        },
    },

    methods: {
        setRules: function () {
            const transformedField = Str.transformKeysToStar(this.name);
            let rules = Store.getRules(transformedField);

            if (rules === undefined) {
                rules = [];

                if (this.rules !== '') {
                    this.rules.split('|').forEach(rule => {
                        const [ruleName, ruleParameter] = rule.split(':');
                        const ruleString = ruleName.charAt(0).toUpperCase() + ruleName.substring(1);
                        const RuleClass = require(`../../Security/Rules/${ruleString}Rule`).default;
                        rules.push(new RuleClass(ruleParameter));
                    });
                }

                Store.setRules(transformedField, rules);
            }

            this.$rules = rules;
        },
        change:   function (value) {
            Store.setData(this.name, value);

            if (this.$rules.length > 0) {
                Store.checkData(this.name, value, this.$rules);
            }
        },
    }
};
