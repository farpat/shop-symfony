<template>
    <span v-if="label && required" class="text-danger">*</span>
</template>

<script>
    export default {
        props: {
            label:    {type: [String, Boolean], required: true},
            required: {type: Boolean, required: true},
        }
    }
</script>
