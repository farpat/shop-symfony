import { jsonPatch, jsonDelete } from '@farpat/api'

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

  /**
   *
   * @param {Function} dispatch
   * @param {Object} reference
   * @param {Number} quantity
   * @returns {Promise<void>}
   */
  async updateItemQuantityForRedux (dispatch, reference, quantity) {
    dispatch({ type: 'SET_CART_ITEM_IS_LOADING', reference, isLoading: true })

    try {
      const response = jsonPatch(`/cart-items/${reference.id}`, { quantity })
      dispatch({ type: 'UPDATE_ITEM_QUANTITY', reference: response.reference, quantity })
    } catch (error) {
      console.error(error)
    } finally {
      dispatch({ type: 'SET_CART_ITEM_IS_LOADING', reference, isLoading: false })
    }
  }

  /**
   *
   * @param {Function} dispatch
   * @param {Object} reference
   * @returns {Promise<void>}
   */
  async deleteItemForRedux (dispatch, reference) {
    dispatch({ type: 'SET_CART_ITEM_IS_LOADING', reference, isLoading: true })

    try {
      jsonDelete(`/cart-items/${reference.id}`)
      dispatch({ type: 'DELETE_ITEM', reference })
    } catch (error) {
      console.error(error)
    } finally {
      dispatch({ type: 'SET_CART_ITEM_IS_LOADING', reference, isLoading: false })
    }
  }
}

export default new CartService()
