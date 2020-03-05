<template>
    <div :class="getClass" role="alert">{{ error }}</div>
</template>

<script>
    export default {
        props: {
            error: {type: String, required: true},
        },
        computed: {
            getClass: function () {
                return {
                    'invalid-feedback': true,
                    'd-block': this.error
                };
            }
        }
    }
</script>
