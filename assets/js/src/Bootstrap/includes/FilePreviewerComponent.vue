<template>
    <div v-show="files.length > 0" class="row mt-2">
        <div v-for="file in files" :class="getRenderPreviewFileClass">
            <img v-if="getIcon(file) === 'image'" :src="renderSource(file)" :alt="file.name"
                 class="img-thumbnail img-fluid">
            <i v-else :class="'fas fa-4x fa-'+getIcon(file)"></i>
            <br>
            <p class="text-muted" style="font-size:0.9rem;text-overflow: ellipsis; overflow: hidden;">
                {{ file.name }}
            </p>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            files: {type: Array, required: true},
            preview: {type: String, required: true},
        },
        computed: {
            getRenderPreviewFileClass: function () {
                let classes = {
                    'xs': 2,
                    'sm': 4,
                    'md': 6,
                    'xl': 12
                };

                return `col-${classes[this.preview]} mb-2 text-center`;
            }
        },
        methods: {
            renderSource: function (file) {
                return URL.createObjectURL(file);
            },

            getIcon(file) {
                const format = file.type;

                if (format.search('image') !== -1) {
                    return 'image';
                } else if (format.search('audio') !== -1) {
                    return 'file-audio';
                } else if (format.search('video') !== -1) {
                    return 'file-video';
                } else if (format.search('text') !== -1) {
                    return 'file-alt';
                } else if (format.search('zip') !== -1 || format.search('compressed') !== -1 || format.search('rar') !== -1) {
                    return 'archive';
                } else if (format.search('pdf') !== -1) {
                    return 'file-pdf';
                } else if (format.search('word') !== -1) {
                    return 'file-word';
                } else if (format.search('powerpoint') !== -1 || format.search('presentation') !== -1) {
                    return 'file-powerpoint';
                }

                return 'file';
            },
        }
    }
</script>
