class CartService {
  constructor () {
    this.data = {}
    this.baseUrl = window.location.origin
  }

  /**
   *
   * @param {HTMLElement} headCartElement
   */
  createInitialData (headCartElement) {
    this.data = {
      quantities: {},
      items: JSON.parse(headCartElement.dataset.items),
      purchaseUrl: headCartElement.dataset.purchaseUrl,
      currency: document.querySelector('#cart-nav').dataset.currency,
      itemInLoading: {}
    }
  }

  itemInLoading (reference, isLoading) {
    this.data = {
      ...this.data,
      itemInLoading: {
        ...this.data.itemInLoading,
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
      items: {
        ...this.data.items,
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
      items: {
        ...this.data.items,
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
    const items = { ...this.data.items }
    delete items[reference.id]

    this.data = {
      ...this.data,
      items
    }

    return this
  }
}

export default new CartService()
