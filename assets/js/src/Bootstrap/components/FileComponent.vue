<template>
    <div class="form-group">
        <label class="col-form-label" v-html="label" v-if="label"></label>

        <required-component :label="label" :required="isRequired"></required-component>

        <div :class="getContainerClass">

            <div class="custom-file">
                <input :accept="getAccept" :class="getInputClass" :id="getId"
                       :multiple="multiple"
                       :name="getName" :required="isRequired"
                       @change="change(Array.from($event.target.files))" type="file">

                <label class="custom-file-label">{{ getIndicator }}</label>
            </div>

            <div class="input-group-append" v-if="isDisplayingDeleteButton">
                <button @click="onDeleteFile" class="btn btn-outline-secondary" type="button">&times;</button>
            </div>
        </div>

        <error-component :error="getError"></error-component>

        <file-previewer-component :files="getFiles" :preview="preview" v-if="preview"></file-previewer-component>
    </div>
</template>

<script>
    import Str from "../../String/Str";
    import RequiredComponent from "../includes/RequiredComponent";
    import ErrorComponent from "../includes/ErrorComponent";
    import FilePreviewerComponent from '../includes/FilePreviewerComponent';
    import FormElementMixin from "../includes/FormElementMixin";

    export default {
        components: {
            ErrorComponent,
            RequiredComponent,
            FilePreviewerComponent
        },
        mixins:     [FormElementMixin],
        data:       function () {
            return {
                rules: [],
            };
        },
        props:      {
            preview:          {type: String, default: ''},
            multiple:         {type: Boolean, default: false},
            initialIndicator: {type: String, required: true},
        },
        mounted:    function () {
            this.$inputFile = this.$el.querySelector('input');
        },
        methods:    {
            onDeleteFile: function () {
                this.$inputFile.value = '';
                this.change([]);
            }
        },

        computed: {
            getFiles:                 function () {
                return this.getValue || [];
            },
            getAccept:                function () {
                if (this.rules.find(rule => rule.name === 'image') !== undefined) {
                    return 'image/*';
                } else if (this.rules.find(rule => rule.name === 'audio') !== undefined) {
                    return 'audio/*';
                }

                return '';
            },
            isDisplayingDeleteButton: function () {
                return !this.isRequired && this.getFiles.length > 0
            },
            getContainerClass:        function () {
                return {
                    'input-group': this.isDisplayingDeleteButton,
                };
            },
            getInputClass:            function () {
                return {
                    'custom-file-input': true,
                    'is-invalid':        this.getError
                };
            },
            getIndicator:             function () {
                const filesCount = this.getFiles.length;

                if (filesCount === 0) {
                    return this.initialIndicator;
                }

                const totalSize = this.getFiles.reduce((acc, file) => acc + file.size, 0);
                const information = filesCount === 1 ? this.getFiles[0].name : `${filesCount} files`;

                return `${information} (${Str.bytesToSize(totalSize)})`;
            }
        }
    }
</script>
