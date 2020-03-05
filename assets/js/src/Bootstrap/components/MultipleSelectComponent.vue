<template>
    <div class="form-group">
        <label :for="getId" v-if="label" class="col-form-label" v-html="label"></label>

        <required-component :label="label" :required="isRequired"></required-component>

        <select :name="getName" :class="getSelectClass" :id="getId" multiple v-model="getValues">
            <option v-for="option in options" :key="option.value || option.id" :value="getOptionValue(option)">
                {{ option.label }}
            </option>
        </select>

        <error-component :error="getError"></error-component>
    </div>
</template>

<script>
    import 'multiple-select';
    import RequiredComponent from "../includes/RequiredComponent";
    import ErrorComponent from "../includes/ErrorComponent";
    import FormElementMixin from "../includes/FormElementMixin";

    export default {
        components: {
            ErrorComponent,
            RequiredComponent
        },
        mixins: [FormElementMixin],
        props: {
            filter: {type: Boolean, default: false},
            placeholder: {type: String, default: ''},
            options: {type: Array, required: true},
            multiple: {type: Boolean, default: true}
        },
        mounted: function () {
            const element = this.$el.querySelector('#' + this.getId);

            this.$allValues = [];
            this.mountJQueryComponent(element);

            this.$msParent = element.nextElementSibling;
            this.setListeners();
        },
        computed: {
            getSelectClass: function () {
                return {
                    'form-control': true,
                    'is-invalid': this.getError
                };
            },
            getValues: function () {
                return this.getValue || [];
            }
        },
        methods: {
            mountJQueryComponent: function (element) {
                $(element).multipleSelect({
                    width: '100%',
                    filter: this.filter,
                    placeholder: this.placeholder,
                });
            },

            setListeners() {
                this.$msParent.querySelectorAll('input[type="checkbox"]').forEach(option => {
                    if (option.attributes.getNamedItem('value') !== null) {
                        this.$allValues.push(option.value);
                    }

                    option.addEventListener('change', () => {
                        let newValue;

                        if (option.attributes.value === undefined) { //select all
                            newValue = option.checked ? this.$allValues : [];
                        } else {
                            newValue = option.checked ? this.getValues.concat(option.value) : this.getValues.filter(v => option.value !== v);
                        }

                        this.change(newValue);

                        if (this.getError) {
                            this.$msParent.classList.add('is-invalid');
                            this.$msParent.firstChild.lastChild.classList.add('d-none');
                        } else {
                            this.$msParent.classList.remove('is-invalid');
                            this.$msParent.firstChild.lastChild.classList.remove('d-none');
                        }
                    });
                });
            },
            getOptionValue: function (item) {
                if (item.value == 0) {
                    return item.value;
                }

                return item.value || item.id;
            },
        }
    }
</script>
