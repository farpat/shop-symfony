import React from 'react'
import PropTypes from 'prop-types'
import ReferenceSliderComponent from './ReferenceSliderComponent'
import Str from '../../../../../src/Str'
import { connect } from 'react-redux'
import Requestor from '@farpat/api'

function ProductReferenceComponent ({ currentReference, currency, isLoading, quantities, cartItems, updateQuantity, addInCart }) {
  const getQuantity = function (reference) {
    return quantities[reference.id] || 1
  }

  const getCartItem = function (reference) {
    return cartItems[reference.id]
  }

  const isCurrentLoading = function (reference) {
    const isCurrentLoading = isLoading[reference.id]
    return isCurrentLoading !== undefined ? isLoading : false
  }

  return (
    <article className="row">
      {
        currentReference.mainImage &&
        <div className="col-md-8">
          <ReferenceSliderComponent
            currentReference={currentReference}
          />
        </div>
      }
      <div className="col-md">
        <ul className="list-unstyled">
          <li className="mb-5">
            {
              Str.toLocaleCurrency(currentReference.unitPriceIncludingTaxes, currency)
            }
          </li>
          <li>
            {
              getCartItem(currentReference) ?
                <div>Already in cart, quantity : {getCartItem(currentReference).quantity}</div> :
                <div>
                  Quantity: &nbsp;
                  <input
                    type="number" min="1" className="cart-item-quantity"
                    value={getQuantity(currentReference)}
                    onChange={event => updateQuantity(currentReference, Number.parseInt(event.target.value))}
                  />


                  {
                    isCurrentLoading(currentReference) ?
                      <button className="btn btn-primary btn-sm ml-3" disabled>
                        <i className="fa fa-shopping-cart"/> Loading ...
                      </button> :
                      <button className="btn btn-primary btn-sm ml-3"
                              onClick={() => addInCart(currentReference, getQuantity(currentReference))}>
                        <i className="fa fa-shopping-cart"/> Add in cart
                      </button>
                  }
                </div>
            }
          </li>
        </ul>
      </div>
    </article>
  )
}

// noinspection JSDeprecatedSymbols
ProductReferenceComponent.propTypes = {
  currentReference: PropTypes.shape({
    id                     : PropTypes.number.isRequired,
    label                  : PropTypes.string.isRequired,
    mainImage              : PropTypes.shape({
      urlThumbnail: PropTypes.string.isRequired,
      altThumbnail: PropTypes.string.isRequired
    }),
    unitPriceIncludingTaxes: PropTypes.number.isRequired
  }),
  currency        : PropTypes.string.isRequired,
  quantities      : PropTypes.object.isRequired,
  cartItems       : PropTypes.object.isRequired,

  updateQuantity: PropTypes.func.isRequired,
  addInCart     : PropTypes.func.isRequired
}

const mapStateToProps = (state) => {
  return {
    quantities: state.cart.quantities,
    isLoading : state.cart.quantitiesInLoading,
    cartItems : state.cart.cartItems
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    updateQuantity: (reference, quantity) => dispatch({ type: 'UPDATE_QUANTITY', reference, quantity }),
    addInCart     : async (reference, quantity) => {
      try {
        dispatch({ type: 'SET_QUANTITY_IS_LOADING', reference, isLoading: true })

        const response = await Requestor.newRequest().post('/cart-items', {
          productReferenceId: reference.id,
          quantity
        })
        dispatch({ type: 'ADD_IN_CART', reference: response.reference, quantity })
        dispatch({ type: 'SET_QUANTITY_IS_LOADING', reference, isLoading: false })
      } catch (error) {
        console.error(error)
      }
    }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(ProductReferenceComponent)
