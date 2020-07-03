import React from 'react'
import PropTypes from 'prop-types'
import Str from '../../../../../src/Str'
import { connect } from 'react-redux'
import Requestor from '@farpat/api'

function ItemComponent ({ item, currency, updateItemQuantity, isLoading, deleteItem }) {
  const changeQuantity = function (event) {
    const quantity = Number.parseInt(event.target.value)
    if (quantity > 0) {
      updateItemQuantity(item.reference, quantity)
    }
  }

  const goToReference = function (event) {
    event.preventDefault()
    const url = item.reference.url
    const referenceUrlObject = new URL(window.location.origin + url)
    const currentUrlObject = new URL(window.location.href)

    window.location.href = referenceUrlObject.pathname === currentUrlObject.pathname ?
      referenceUrlObject.origin + referenceUrlObject.pathname + '?r=1' + referenceUrlObject.hash :
      url
  }

  const isCurrentLoading = function () {
    const isCurrentLoading = isLoading[item.reference.id]
    return isCurrentLoading !== undefined ? isLoading : false
  }

  return (
    <tr className="header-cart-item">
      <td>
        <input type="number" className="cart-item-quantity" value={item.quantity} onChange={changeQuantity}/>
      </td>
      <td className="header-cart-item-td-label">
        {
          item.reference.mainImage &&
          <img src={item.reference.mainImage.urlThumbnail} alt={item.reference.mainImage.altThumbnail} width={72}
               className='mr-2'/>
        }
        <a href={item.reference.url} onClick={goToReference}>{item.reference.label}</a>
      </td>
      <td>
        {Str.toLocaleCurrency(item.reference.unitPriceIncludingTaxes, currency)}
      </td>
      <td className="header-cart-item-td-icon">
        {
          isCurrentLoading() ?
            <span><i className="fas fa-spinner spinner"></i></span> :
            <button className="btn btn-sm p-0" type="button" onClick={() => deleteItem(item.reference)}>
              <i className="fas fa-times"></i>
            </button>
        }
      </td>
    </tr>
  )
}

ItemComponent.propTypes = {
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

const mapStateToProps = (state) => {
  return {
    isLoading: state.cart.cartItemsInLoading
  }
}
const mapStateToDispatch = (dispatch) => {
  return {
    updateItemQuantity: async (reference, quantity) => {
      dispatch({ type: 'SET_CART_ITEM_IS_LOADING', reference, isLoading: true })

      try {
        const response = await Requestor.newRequest().patch(`/cart-items/${reference.id}`, { quantity })
        dispatch({ type: 'UPDATE_ITEM_QUANTITY', reference: response.reference, quantity })
      } catch (error) {
        console.error(error)
      } finally {
        dispatch({ type: 'SET_CART_ITEM_IS_LOADING', reference, isLoading: false })
      }
    },
    deleteItem        : async (reference) => {
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
  }
}

export default connect(mapStateToProps, mapStateToDispatch)(ItemComponent)
