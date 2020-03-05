<template>
    <div class="form-group">
        <label :for="getId" class="col-form-label" v-html="label" v-if="label"></label>
        <required-component :label="label" :required="isRequired"></required-component>

        <div class="input-group">
            <div class="input-group-prepend"><span class="input-group-text">&euro;</span></div>

            <input :name="getName" :value="getValue" type="hidden">

            <input :autofocus="autofocus" :class="getInputClass" :id="getId" :key="getId" :placeholder="placeholder"
                   :required="isRequired" :value="getFormattedValue" @blur="onBlur" @focus="onFocus"
                   @change="changeValue($event.target.value)" type="text">
        </div>

        <error-component :error="getError"></error-component>
    </div>
</template>


<script>
    import RequiredComponent from "../includes/RequiredComponent";
    import ErrorComponent from "../includes/ErrorComponent";
    import FormElementMixin from "../includes/FormElementMixin";
    import Str from "../../String/Str";

    export default {
        components: {RequiredComponent, ErrorComponent},
        mixins: [FormElementMixin],
        data: function () {
            return {
                isFocused: false,
            };
        },
        props: {
            placeholder: {type: String, default: ''},
            autofocus: {type: Boolean, default: false},
        },
        methods: {
            changeValue: function (value) {
                value = value.replace(/,/g, '.');
                const parsedValue = value === '' || isNaN(value) ? value : parseFloat(value);
                this.change(parsedValue);
            },
            onFocus: function () {
                this.isFocused = true;

                if (this.getValue == 0) {
                    this.change('');
                }
            },
            onBlur: function () {
                this.isFocused = false;
            }
        },
        computed: {
            getFormattedValue: function () {
                if (!this.isFocused) {
                    if (this.getValue === '' || isNaN(this.getValue)) {
                        return this.getValue;
                    }

                    return Str.toLocaleCurrency(this.getValue);
                }

                return this.getValue;
            },
            getInputClass: function () {
                return {
                    'form-control-plaintext': this.plain,
                    'form-control': !this.plain,
                    'is-invalid': this.getError
                };
            },
            getContainerClass: function () {
                return (this.after || this.before) ? 'input-group' : ''
            },
        }
    }
</script>
