import PropTypes from 'prop-types'

/** PROPTYPES **/
const referencePropType = PropTypes.shape({
  url                    : PropTypes.string.isRequired,
  label                  : PropTypes.string.isRequired,
  unit_price_including_taxes: PropTypes.number.isRequired,
  unit_price_excluding_taxes: PropTypes.number.isRequired,
  mainImage              : PropTypes.shape({
    url_thumbnail: PropTypes.string.isRequired,
    alt_thumbnail: PropTypes.string.isRequired
  })
})

export const CartComponentPropTypes = {
  cartItems  : PropTypes.objectOf(PropTypes.shape({
    quantity : PropTypes.number.isRequired,
    reference: referencePropType
  })).isRequired,
  purchaseUrl: PropTypes.string.isRequired,
  currency   : PropTypes.string.isRequired
}

export const ItemComponentPropTypes = {
  item     : PropTypes.shape({
    quantity : PropTypes.number.isRequired,
    reference: referencePropType
  }),
  currency : PropTypes.string.isRequired,
  isLoading: PropTypes.object.isRequired,

  updateItemQuantity: PropTypes.func.isRequired
}

export const TotalComponentPropTypes = {
  cartItems: PropTypes.objectOf(PropTypes.shape({
    quantity : PropTypes.number.isRequired,
    reference: PropTypes.shape({
      unit_price_including_taxes: PropTypes.number.isRequired,
      unit_price_excluding_taxes: PropTypes.number.isRequired
    })
  })).isRequired,
  currency : PropTypes.string.isRequired
}

/** FUNCTIONS IN COMPONENT **/
/**
 *
 * @param {Event} event
 * @param {String} referenceUrl
 */
export function goToReference (event, referenceUrl) {
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
export function isItemLoading (isLoading, referenceId) {
  const isCurrentLoading = isLoading[referenceId]
  return isCurrentLoading !== undefined ? isCurrentLoading : false
}
