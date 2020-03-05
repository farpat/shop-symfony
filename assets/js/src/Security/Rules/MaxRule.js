import Str from "../../String/Str";

export default class MaxRule {
    constructor(max, type = 'string') {
        this.type = type;
        this.params = type === 'file' ? Str.sizeToBytes(max) : max;
        this.name = 'max';
    }

    /**
     * Vérifie que le poids de la liste des fichiers ne dépasse pas size
     * @param {Array} files
     * @return boolean
     */
    verifyFileSize(files) {
        let totalSize = files.reduce((acc, file) => acc + file.size, 0);
        return totalSize <= this.params;
    }

    check(value) {
        if (this.type === 'file') {
            return this.verifyFileSize(value);
        }

        if (this.type === 'string') {
            return (value.length === 0 || value.length < this.params);
        }

        if (this.type === 'number') {
            return value <= this.params;
        }

        return false;
    }
}
