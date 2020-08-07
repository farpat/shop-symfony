import React from 'react'
import { hot } from 'react-hot-loader/root'
import { connect } from 'react-redux'
import ItemComponent from './Cart/ItemComponent'
import TotalComponent from './Cart/TotalComponent'
import { CartComponentPropTypes } from '../cartCommon'

function CartComponent ({ cartItems, purchaseUrl, currency }) {
  const referenceIds = Object.keys(cartItems)

  return <>
    <button className={`nav-link nav-link-dropdown ${referenceIds.length > 0 ? '' : 'd-none'}`}>
      <i className="fas fa-shopping-cart"/> - {referenceIds.length}
    </button>
    <div className={`nav-dropdown-items header-cart ${referenceIds.length > 0 ? '' : 'd-none'}`}>
      <table className='table table-borderless table-hover table-responsive'>
        <tbody>
        {
          referenceIds.map(referenceId => <ItemComponent
            item={cartItems[referenceId]} key={referenceId} currency={currency}/>)
        }
        </tbody>

        <TotalComponent cartItems={cartItems} currency={currency} purchaseUrl={purchaseUrl}/>
      </table>
    </div>
  </>
}

CartComponent.propTypes = CartComponentPropTypes

const mapStateToProps = (state) => {
  return {
    cartItems  : state.cart.cartItems,
    purchaseUrl: state.cart.purchaseUrl,
    currency   : state.cart.currency
  }
}

const mapDispatchToProps = (dispatch) => {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(hot(CartComponent))
