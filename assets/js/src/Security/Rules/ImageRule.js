export default class ImageRule {
    constructor() {
        this.name = 'image';
    }

    check(value) {
        if (value.length === 0) {
            return true;
        }

        const filesLength = value.length;
        for (let i = 0; i < filesLength; i++) {
            /** @var File file */
            let file = value[i];
            if (file.type.substring(0, 5) !== 'image') {
                return false;
            }
        }

        return true;
    }
}
