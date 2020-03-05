<template>
    <div class="modal fade" tabindex="-1" role="dialog">
        <div :class="getModalClass" class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p :class="getModalTitleClass" class="modal-title" v-if="title">{{ title }}</p>
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" v-html="content">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-link" data-dismiss="modal" type="button">
                        {{ __(closeButtonLabel) }}
                    </button>
                    <button v-if="displayOk" :class="getOkButtonClass" class="btn" type="button">
                        {{ __(okButtonLabel) }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import 'bootstrap/js/dist/modal';
    import TranslationMixin from "../../Translation/TranslationMixin";

    export default {
        mixins:   [TranslationMixin],
        props:    {
            type:             {type: String, default: ''},
            title:            {type: String, default: ''},
            size:             {type: String, default: ''},
            content:          {type: String, required: true},
            closeButtonLabel: {type: String, default: 'Close'},
            okButtonLabel:    {type: String, default: 'OK'},
            displayOk:        {type: Boolean, default: false}
        },
        computed: {
            getModalClass: function () {
                return this.size ? 'modal-' + this.size : null;
            },
            getModalTitleClass: function () {
                return this.type ? 'text-' + this.type : null;
            },
            getOkButtonClass: function () {
                return this.type ? 'btn-' + this.type : null;
            }
        },
        mounted:  function () {
            $(this.$el).modal('show');
            $(this.$el).on('hidden.bs.modal', () => this.$destroy());
        },
        destroyed: function () {
            this.$el.remove();
        },
    }
</script>
