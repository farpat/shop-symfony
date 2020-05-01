import Str from "../String/Str"

class Arr {
    /**
     *
     * @param {Object} object
     * @param {Array} keys
     * @returns {undefined|*}
     */
    getNestedProperty(object, keys) {
        for (let i = 0; i < keys.length; i++) {
            let key = keys[i]

            if (!object || !object.hasOwnProperty(key)) {
                return undefined
            }

            object = object[key]
        }

        return object
    }

    /**
     *
     * @param {Object} obj
     * @return {Boolean}
     */
    isAssociative(obj) {
        const keys = Object.keys(obj)

        if (keys.length === 0) {
            return false
        }

        return !!!keys.find(key => !Str.isNumeric(key))
    }

    /**
     *
     * @param {Object} object
     * @returns {undefined|*}
     */
    getFirstValue(object) {
        const keys = Object.keys(object)

        if (keys.length === 0) {
            return undefined
        }

        return object[keys[0]]
    }

    /**
     *
     * @param {Object} obj
     * @returns {undefined|*}
     */
    last(obj) {
        const keys = Object.keys(obj)

        if (keys.length === 0) {
            return null
        }

        return obj[keys[keys.length - 1]]
    }

    /**
     *
     * @param {Object|Array} arr
     * @returns {boolean}
     */
    isEmpty(arr) {
        if (arr.length !== undefined && arr.length === 0) {
            return true
        }

        if (typeof arr === 'object' && Object.keys(arr).length === 0) {
            return true
        }

        return false
    }

    /**
     *
     * @param {Object} object
     * @param {String} string
     * @param {*} value
     */
    returnNestedObject(object, string, value) {
        let nestedObject = {...object} //To ensure don't reset object's reference
        let nextObject = {}

        const matches = Array.from(string.matchAll(/\[?([\w_-]+)\]?/g))

        for (let i = 0; i < matches.length; i++) {
            let key = matches[i][1]

            if (i === 0) { //start
                if (nestedObject[key] === undefined) {
                    nestedObject[key] = {}
                }
                nextObject = nestedObject[key]
            } else if (i === matches.length - 1) { //end
                nextObject[key] = value
            } else { //middle
                if (nextObject[key] === undefined) {
                    nextObject[key] = {}
                }
                nextObject = nextObject[key]
            }
        }

        return nestedObject
    }
}

export default new Arr()
