/**
 * @property {string} baseUrl
 * @property {data} Object
 */
class ProductService {
    constructor() {
        this.baseUrl = window.location.origin + window.location.pathname
        this.data = {}
    }

    updateActivatedSliderIndexByReference(reference, sliderIndex) {
        this.data = {
            ...this.data,
            activatedSliderIndexByReference: {
                ...this.data.activatedSliderIndexByReference,
                [reference.id]: sliderIndex
            }
        }

        return this
    }

    updateReference(newCurrentReference) {
        this.changeHash(newCurrentReference)

        this.data = {
            ...this.data,
            currentReference: newCurrentReference
        }

        return this
    }

    getData() {
        return this.data
    }

    /**
     *
     * @param {HTMLDivElement|null} productElement
     */
    createInitialData(productElement) {
        if (!productElement) {
            this.data = {}

            return
        }

        const {productReferences: productReferencesInString, currency} = productElement.dataset
        const productReferences = JSON.parse(productReferencesInString)
        const currentReference = this.getCurrentReferenceFromHash(window.location.hash, productReferences)

        let activatedSliderIndexByReference = {}
        productReferences.map(reference => activatedSliderIndexByReference[reference.id] = 0)

        this.data = {
            productReferences,
            currentReference,
            currency,
            activatedSliderIndexByReference
        }
    }

    changeHash(currentReference) {
        window.history.replaceState({}, '', this.baseUrl + '#' + currentReference.id)
    }

    getCurrentReferenceFromHash(hash, productReferences) {
        let currentReference

        if (hash === '') {
            currentReference = productReferences[0]
            this.changeHash(currentReference)
        } else {
            currentReference = productReferences.find(productReference => '#' + productReference.id === hash)

            if (currentReference === undefined) {
                currentReference = productReferences[0]
                this.changeHash(currentReference)
            }
        }

        if (window.location.href.includes('?r=1')) { //forcing remove ?r=1 in url
            this.changeHash(currentReference)
        }

        return currentReference
    }
}

export default new ProductService()
