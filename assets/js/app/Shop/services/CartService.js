class CartService {
  constructor () {
    this.data = {}
    this.baseUrl = window.location.origin
  }

  /**
   *
   * @param Object cartItems
   * @returns {{totalIncludingTaxes: number, totalPriceExcludingTaxes: number, totalPriceIncludingTaxes: number}}
   */
  getPrices (cartItems) {
    let totalPriceExcludingTaxes = 0
    let totalPriceIncludingTaxes = 0

    Object.keys(cartItems).map(referenceId => {
      const item = cartItems[referenceId]
      totalPriceExcludingTaxes += item.quantity * item.reference.unitPriceExcludingTaxes
      totalPriceIncludingTaxes += item.quantity * item.reference.unitPriceIncludingTaxes
    })

    return {
      totalPriceExcludingTaxes,
      totalPriceIncludingTaxes,
      totalIncludingTaxes: totalPriceIncludingTaxes - totalPriceExcludingTaxes
    }
  }

  /**
   *
   * @param {HTMLElement} headCartElement
   */
  createInitialData (headCartElement) {
    this.data = {
      quantities         : {},
      quantitiesInLoading: {},

      cartItems         : JSON.parse(headCartElement.dataset.cartItems),
      cartItemsInLoading: {},

      purchaseUrl: headCartElement.dataset.purchaseUrl,
      currency   : document.querySelector('#cart-nav').dataset.currency,
    }
  }

  setCartItemInLoading (reference, isLoading) {
    this.data = {
      ...this.data,
      cartItemsInLoading: {
        ...this.data.cartItemsInLoading,
        [reference.id]: isLoading
      }
    }

    return this
  }

  setQuantityInLoading (reference, isLoading) {
    this.data = {
      ...this.data,
      quantitiesInLoading: {
        ...this.data.quantitiesInLoading,
        [reference.id]: isLoading
      }
    }

    return this
  }

  updateQuantity (reference, quantity) {
    this.data = {
      ...this.data,
      quantities: {
        ...this.data.quantities,
        [reference.id]: quantity
      }
    }

    return this
  }

  updateItemQuantity (reference, quantity) {
    this.data = {
      ...this.data,
      cartItems : {
        ...this.data.cartItems,
        [reference.id]: { quantity, reference }
      },
      quantities: {
        ...this.data.quantities,
        [reference.id]: 1
      }
    }

    return this
  }

  addInCart (reference, quantity) {
    this.data = {
      ...this.data,
      cartItems : {
        ...this.data.cartItems,
        [reference.id]: { quantity, reference }
      },
      quantities: {
        ...this.data.quantities,
        [reference.id]: 1
      }
    }

    return this
  }

  getData () {
    return this.data
  }

  deleteItem (reference) {
    const cartItems = { ...this.data.cartItems }
    delete cartItems[reference.id]

    this.data = {
      ...this.data,
      cartItems
    }

    return this
  }
}

export default new CartService()
