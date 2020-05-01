class CartService {
    constructor() {
        this.data = {}
    }

    /**
     *
     * @param {HTMLElement} headCartElement
     */
    loadData(headCartElement) {
        this.data = {
            quantities: {},
            items:      JSON.parse(headCartElement.dataset.items)
        }
    }

    updateQuantity(reference, quantity) {
        this.data = {
            ...this.data,
            quantities: {
                ...this.data.quantities,
                [reference.id]: quantity
            }
        }

        return this
    }

    addInCart(reference, quantity) {
        this.data = {
            ...this.data,
            items: {
                ...this.data.items,
                [reference.id]: {quantity, reference}
            }
        }

        return this
    }

    getData() {
        console.log(this.data)
        return this.data
    }
}

export default new CartService()
