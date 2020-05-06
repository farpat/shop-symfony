import Input from "./Input"
import Str from "../String/Str"

export default class Autocomplete {
    constructor(options) {
        this.setOptions(options)
        this.init()
    }

    setOptions(options) {
        var defaultOptions = {
            selector:   0,
            source:     0,
            minChars:   3,
            delay:      150,
            offsetLeft: 0,
            offsetTop:  1,
            cache:      1,
            menuClass:  undefined,
            renderItem: function (item, search) {
                if (!item.label) {
                    return ''
                }

                return `<div class="autocomplete-suggestion" data-val="${item}">${Str.markValueIntoText(search, item.label)}</div>`
            },
            onSelect:   function (e, term, item) {
            }
        }
        this.options = {...defaultOptions, ...options}
    }

    init() {
        this.inputElements = this.options.selector instanceof HTMLElement ? [this.options.selector] : document.querySelectorAll(this.options.selector)
        this.inputs = []
        this.inputElements.forEach((inputElement) => {
            this.inputs.push(new Input(inputElement, this.options))
        })
    }

    destroy() {
        this.inputs.forEach(function (input) {
            input.destroy()
        })
    }
}
