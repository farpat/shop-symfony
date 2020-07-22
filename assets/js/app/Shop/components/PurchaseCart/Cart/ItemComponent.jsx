import React from 'react'
import PropTypes from 'prop-types'
import Str from '../../../../../src/Str'
import { connect } from 'react-redux'
import Requestor from '@farpat/api'
import { deleteItem, goToReference, isItemLoading, ItemComponentPropTypes, updateItemQuantity } from '../../cartCommon'

function ItemComponent ({ item, currency, updateItemQuantity, isLoading, deleteItem }) {
  const changeQuantity = function (event) {
    const quantity = Number.parseInt(event.target.value)
    if (quantity > 0) {
      updateItemQuantity(item.reference, quantity)
    }
  }

  return <tr className="purchase-cart-item">
    <td>
      <input type="number" className="cart-item-quantity" value={item.quantity}
             onChange={changeQuantity}/>
    </td>
    <td className="purchase-cart-item-label">
      {
        item.reference.mainImage &&
        <img src={item.reference.mainImage.urlThumbnail} alt={item.reference.mainImage.altThumbnail} width={72}
             className='purchase-cart-item-image'/>
      }
      <a href={item.reference.url}
         onClick={event => goToReference(event, item.reference.url)}>{item.reference.label}</a>
    </td>
    <td>
      {Str.toLocaleCurrency(item.reference.unitPriceIncludingTaxes, currency)}
    </td>
    <td className="purchase-cart-item-icon">
      {
        isItemLoading(isLoading, item.reference.id) ?
          <button className="btn btn-link" type="button">
            <i className="fas fa-spinner spinner"/>
          </button> :
          <button className="btn btn-link" type="button" onClick={() => deleteItem(item.reference)}>
            <i className="fas fa-times"/>
          </button>
      }
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
      updateItemQuantity(dispatch, reference, quantity)
    },
    deleteItem        : (reference) => {
      deleteItem(dispatch, reference)
    }
  }
}

export default connect(mapStateToProps, mapStateToDispatch)(ItemComponent)
