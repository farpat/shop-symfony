import React from 'react'
import PropTypes from 'prop-types'
import ReferenceSliderComponent from './ReferenceSliderComponent'
import Str from '../../../../../src/Str'
import { connect } from 'react-redux'
import Requestor from '@farpat/api'
import Translation from '../../../../../src/Translation'

function ProductReferenceComponent ({ currentReference, currency, isLoading, quantities, cartItems, updateQuantity, addInCart }) {
  const getQuantity = function (reference) {
    return quantities[reference.id] || 1
  }

  const currentCartItem = cartItems[currentReference.id]

  const isCurrentLoading = function (reference) {
    const isCurrentLoading = isLoading[reference.id]
    return isCurrentLoading !== undefined ? isCurrentLoading : false
  }

  return (
    <article className="row">
      {
        currentReference.mainImage &&
        <div className="col-md-8 reference-slider">
          <ReferenceSliderComponent
            currentReference={currentReference}
          />
        </div>
      }
      <div className="col-md reference-details-wrapper">
        <div className="reference-addtocart">
          {
            currentCartItem ?
              <>
                <input
                  type="number" min="1" className="cart-item-quantity"
                  value={currentCartItem.quantity} readOnly
                />
                <button disabled>
                  {Translation.get('Added')}
                </button>
              </> :
              <>
                <input
                  type="number" min="1" className="cart-item-quantity"
                  value={getQuantity(currentReference)}
                  onChange={event => updateQuantity(currentReference, Number.parseInt(event.target.value))}
                />
                {
                  isCurrentLoading(currentReference) ?
                    <button disabled>
                      <i className="fa fa-shopping-cart"/> Loading ...
                    </button> :
                    <button onClick={() => addInCart(currentReference, getQuantity(currentReference))}>
                      <i className="fa fa-shopping-cart"/> {Translation.get('Add to cart')}
                    </button>
                }
              </>
          }
        </div>
        <div className="reference-price">
          {
            Str.toLocaleCurrency(currentReference.unitPriceIncludingTaxes, currency)
          }
        </div>
      </div>
    </article>
  )
}

// noinspection JSDeprecatedSymbols
ProductReferenceComponent.propTypes = {
  quantities: PropTypes.object.isRequired,
  isLoading : PropTypes.object.isRequired,
  cartItems : PropTypes.object.isRequired,

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
