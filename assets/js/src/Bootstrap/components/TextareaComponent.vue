<template>
    <div class="form-group">
        <label :for="getId" v-if="label" class="col-form-label" v-html="label"></label>

        <required-component :label="label" :required="isRequired"></required-component>

        <textarea :readonly="readonly" :autofocus="autofocus" :id="getId" :class="getInputClass"
                  :name="getName" :value="getValue" @change="change($event.target.value)"
                  :placeholder="placeholder" :required="isRequired"></textarea>

        <error-component :error="getError"></error-component>
    </div>
</template>


<script>

    import RequiredComponent from "../includes/RequiredComponent";
    import ErrorComponent from "../includes/ErrorComponent";
    import FormElementMixin from "../includes/FormElementMixin";

    export default {
        components: {ErrorComponent, RequiredComponent},
        mixins: [FormElementMixin],
        props: {
            readonly: {type: Boolean, default: false},
            plain: {type: String, default: ''},
            placeholder: {type: String, default: ''},
            autofocus: {type: Boolean, default: false},
        },
        computed: {
            getInputClass: function () {
                if (this.plain) {
                    return 'form-control-plaintext';
                }

                return {
                    'form-control': true,
                    'is-invalid': this.getError
                };
            }
        }
    }
</script>
