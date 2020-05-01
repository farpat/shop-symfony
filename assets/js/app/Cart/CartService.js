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
            cartItems:  JSON.parse(headCartElement.dataset.items)
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
            cartItems: {
                ...this.data.cartItems,
                [reference.id]: {quantity, reference}
            }
        }

        return this
    }

    getData() {
        return this.data
    }
}

export default new CartService()
