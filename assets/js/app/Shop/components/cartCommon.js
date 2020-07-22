import PropTypes from 'prop-types'
import Requestor from '@farpat/api'

/** PROPTYPES **/
export const CartComponentPropTypes = {
  cartItems  : PropTypes.objectOf(PropTypes.shape({
    quantity: PropTypes.number.isRequired,

    reference: PropTypes.shape({
      url                    : PropTypes.string.isRequired,
      label                  : PropTypes.string.isRequired,
      unitPriceIncludingTaxes: PropTypes.number.isRequired,
      unitPriceExcludingTaxes: PropTypes.number.isRequired,
      mainImage              : PropTypes.shape({
        urlThumbnail: PropTypes.string.isRequired,
        altThumbnail: PropTypes.string.isRequired
      })
    })
  })).isRequired,
  purchaseUrl: PropTypes.string.isRequired,
  currency   : PropTypes.string.isRequired
}

export const ItemComponentPropTypes = {
  item     : PropTypes.shape({
    quantity: PropTypes.number.isRequired,

    reference: PropTypes.shape({
      url                    : PropTypes.string.isRequired,
      label                  : PropTypes.string.isRequired,
      unitPriceIncludingTaxes: PropTypes.number.isRequired,
      unitPriceExcludingTaxes: PropTypes.number.isRequired,
      mainImage              : PropTypes.shape({
        urlThumbnail: PropTypes.string.isRequired,
        altThumbnail: PropTypes.string.isRequired
      })
    })
  }),
  currency : PropTypes.string.isRequired,
  isLoading: PropTypes.object.isRequired,

  updateItemQuantity: PropTypes.func.isRequired
}

/** FUNCTIONS IN COMPONENT **/
/**
 *
 * @param {Event} event
 * @param {String} referenceUrl
 */
export function goToReference(event, referenceUrl) {
  event.preventDefault()
  const referenceUrlObject = new URL(window.location.origin + referenceUrl)
  const currentUrlObject = new URL(window.location.href)

  window.location.href = referenceUrlObject.pathname === currentUrlObject.pathname ?
    referenceUrlObject.origin + referenceUrlObject.pathname + '?r=1' + referenceUrlObject.hash :
    referenceUrl
}

/**
 *
 * @param {Object} isLoading Object containing in key the "referenceId" and in value if this one is loading or not
 * @param {Number} referenceId
 * @returns {*}
 */
export function isItemLoading(isLoading, referenceId) {
  const isCurrentLoading = isLoading[referenceId]
  return isCurrentLoading !== undefined ? isCurrentLoading : false
}

/**
 *
 * @param {Event} event
 * @param {Function} updateItemQuantity
 */
export const changeQuantity = function (event, updateItemQuantity) {
  const quantity = Number.parseInt(event.target.value)
  if (quantity > 0) {
    updateItemQuantity(item.reference, quantity)
  }
}

/** ACTIONS **/
/**
 *
 * @param {Function} dispatch
 * @param {Object} reference
 * @param {Number} quantity
 * @returns {Promise<void>}
 */
export async function updateItemQuantity (dispatch, reference, quantity) {
  dispatch({ type: 'SET_CART_ITEM_IS_LOADING', reference, isLoading: true })

  try {
    const response = await Requestor.newRequest().patch(`/cart-items/${reference.id}`, { quantity })
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
export async function deleteItem (dispatch, reference) {
  dispatch({ type: 'SET_CART_ITEM_IS_LOADING', reference, isLoading: true })

  try {
    await Requestor.newRequest().delete(`/cart-items/${reference.id}`)
    dispatch({ type: 'DELETE_ITEM', reference })
  } catch (error) {
    console.error(error)
  } finally {
    dispatch({ type: 'SET_CART_ITEM_IS_LOADING', reference, isLoading: false })
  }
}