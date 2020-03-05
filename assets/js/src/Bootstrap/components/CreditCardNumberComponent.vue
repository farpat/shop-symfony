<template>
    <div class="form-group">
        <label :for="getId" class="col-form-label" v-html="label" v-if="label"></label>

        <required-component :label="label" :required="isRequired"></required-component>

        <div class="input-group">
            <input :autofocus="autofocus" :class="getInputClass" :id="getId" :name="getName" :placeholder="placeholder"
                   :readonly="readonly"
                   :required="isRequired" :value="getValue" @change="change($event.target.value)"
                   @input="formatNumber($event.target)"
                   maxlength="20"
                   type="text">

            <div class="input-group-append">
                <span class="input-group-text">
                    <i class="fab fa-cc-visa mx-1"></i>
                    <i class="fab fa-cc-amex mx-1"></i>
                    <i class="fab fa-cc-mastercard mx-1"></i>
                </span>
            </div>


        </div>

        <error-component :error="getError"></error-component>
    </div>
</template>


<script>
    import FormElementMixin from "../includes/FormElementMixin";
    import RequiredComponent from "../includes/RequiredComponent";
    import ErrorComponent from "../includes/ErrorComponent";
    import StrMixin from "../../String/StrMixin";

    export default {
        components: {RequiredComponent, ErrorComponent},
        mixins:     [FormElementMixin, StrMixin],
        props:      {
            readonly:    {type: Boolean, default: false},
            plain:       {type: String, default: ''},
            placeholder: {type: String, default: 'NNNN NNNN NNNN NNNN'},
            autofocus:   {type: Boolean, default: false},
        },
        computed:   {
            getInputClass:     function () {
                if (this.plain) {
                    return 'form-control-plaintext';
                }

                return {
                    'form-control': true,
                    'is-invalid':   this.getError
                };
            },
            getContainerClass: function () {
                if (this.after || this.before) {
                    return 'input-group';
                }

                return '';
            }
        },
        methods:    {
            formatNumber: function (input) {
                let value = input.value;
                input.value = this.formatCardNumber(value);
            }
        }
    }
</script>



