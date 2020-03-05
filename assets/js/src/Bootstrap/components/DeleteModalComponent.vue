<template>
    <modal-component :title="title" size="md" :content="formHtml" type="danger" :close-button-label="translate(closeButtonLabel)" :display-ok="true">
    </modal-component>
</template>


<script>
    import ModalComponent from "./ModalComponent";
    import Requestor from "@farpat/api";
    import TranslationMixin from "../../Translation/TranslationMixin";

    export default {
        components: {ModalComponent},
        mixins: [TranslationMixin],
        props: {
            action: {type: String, required: true},
            title: {type: String, required: true},
            closeButtonLabel: {type: String, default: 'Cancel'}
        },
        computed: {
            formHtml: function () {
                return `
                <form action="${this.action}" method="post">
                    <input type="hidden" name="_token" value="${Requestor.getCsrfToken()}">
                    <input type="hidden" name="_method" value="DELETE">
                </form>`;
            }
        },
    }
</script>
