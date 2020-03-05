<template>
    <div class="form-group">
        <label :for="getId" v-if="label" class="col-form-label" v-html="label"></label>

        <required-component :label="label" :required="isRequired"></required-component>

        <select :name="getName" :id="getId" :class="selectClass" :style="{cursor:'pointer'}" :value="getValue"
                @change="change($event.target.value)">
            <option v-if="placeholder" disabled value="">{{ placeholder }}</option>
            <option v-for="option in options" :value="option.value">{{ option.label }}</option>
        </select>

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
            placeholder: {type: String, default: ''},
            options: {type: Array, required: true},
        },
        computed: {
            getId: function () {
                return this.id || this.name;
            },
            selectClass: function () {
                return {
                    'custom-select': true,
                    'is-invalid': this.getError
                };
            }
        },
    }
</script>
