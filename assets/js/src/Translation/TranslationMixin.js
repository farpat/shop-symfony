import translation from "./Translation";

export default {
    methods: {
        translate: function (key) {
            return translation.get(key);
        },

        __: function (key) {
            return translation.get(key);
        }
    }
}
