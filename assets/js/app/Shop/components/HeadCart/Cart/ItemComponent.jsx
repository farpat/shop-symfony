import React from 'react'
import PropTypes from 'prop-types'
import Str from '../../../../../src/Str'
import { connect } from 'react-redux'
import {  goToReference, isItemLoading, ItemComponentPropTypes } from '../../cartCommon'
import CartService from '../../../services/CartService'

function ItemComponent ({ item, currency, updateItemQuantity, isLoading, deleteItem }) {
  const changeQuantity = function (event) {
    const quantity = Number.parseInt(event.target.value)
    if (quantity > 0) {
      updateItemQuantity(item.reference, quantity)
    }
  }

  return <tr className="header-cart-item">
    <td>
      <input type="number" className="cart-item-quantity" value={item.quantity} onChange={changeQuantity}/>
    </td>
    <td className="header-cart-item-label">
      {
        item.reference.main_image &&
        <img src={item.reference.main_image.url_thumbnail} alt={item.reference.main_image.alt_thumbnail}
             className='header-cart-item-image'/>
      }
      <a className="header-cart-item-link" href={item.reference.url}
         onClick={event => goToReference(event, item.reference.url)}>{item.reference.label}</a>
    </td>
    <td>
      {Str.toLocaleCurrency(item.reference.unit_price_including_taxes, currency)}
    </td>
    <td className="header-cart-item-icon">
      <button className="btn btn-link" type="button" onClick={() => {
        if (!isItemLoading(isLoading, item.reference.id)) {
          deleteItem(item.reference)
        }
      }}>
        {
          isItemLoading(isLoading, item.reference.id) ?
            <i className="fas fa-spinner spinner"/> :
            <i className="fas fa-times"/>
        }
      </button>
    </td>
  </tr>
}

ItemComponent.propTypes = ItemComponentPropTypes

const mapStateToProps = (state) => {
  return {
    isLoading: state.cart.cartItemsInLoading
  }
}
const mapStateToDispatch = (dispatch) => {
  return {
    updateItemQuantity: (reference, quantity) => {
      CartService.updateItemQuantityForRedux(dispatch, reference, quantity)
    },
    deleteItem        : (reference) => {
      CartService.deleteItemForRedux(dispatch, reference)
    }
  }
}

export default connect(mapStateToProps, mapStateToDispatch)(ItemComponent)
