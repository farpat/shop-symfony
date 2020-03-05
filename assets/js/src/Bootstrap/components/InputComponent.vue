<template>
    <div class="form-group">
        <label :for="getId" class="col-form-label" v-html="label" v-if="label"></label>
        <required-component :label="label" :required="isRequired"></required-component>

        <div :class="getContainerClass">
            <div class="input-group-prepend" v-if="before"><span class="input-group-text" v-html="before"></span></div>

            <input :autofocus="autofocus" :class="getInputClass" :id="getId" :maxlength="length" :name="getName"
                   :placeholder="placeholder" :readonly="readonly" :required="isRequired" :style="getStyleCSS"
                   :type="type"
                   :value="getValue" @change="change($event.target.value)" v-bind="getDataAttributes">

            <div class="input-group-append" v-if="after"><span class="input-group-text" v-html="after"></span></div>

            <error-component :error="getError" v-if="displayError"></error-component>
        </div>
    </div>
</template>


<script>
    import FormElementMixin from "../includes/FormElementMixin";
    import RequiredComponent from "../includes/RequiredComponent";
    import ErrorComponent from "../includes/ErrorComponent";

    export default {
        components: {RequiredComponent, ErrorComponent},
        mixins:     [FormElementMixin],
        props:      {
            before:         {type: String, default: ''},
            after:          {type: String, default: ''},
            type:           {type: String, default: 'text'},
            readonly:       {type: Boolean, default: false},
            plain:          {type: String, default: ''},
            placeholder:    {type: String, default: ''},
            autofocus:      {type: Boolean, default: false},
            length:         {type: Number, required: false},
            styleCSS:       {type: Object, required: false},
            displayError:   {type: Boolean, default: true},
        },
        computed:   {
            getStyleCSS:       function () {
                if (this.length) {
                    return {width: `${(this.length * 2) + 1}rem`, ...this.styleCSS};
                }

                return this.styleCSS;
            },
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
        }
    }
</script>



