import str from "./Str";

export default {
    methods: {
        toLocaleCurrency: function (key, currency) {
            return str.toLocaleCurrency(key, currency);
        },

        formatCardNumber: function (text) {
            return str.formatCardNumber(text);
        }
    }
}
