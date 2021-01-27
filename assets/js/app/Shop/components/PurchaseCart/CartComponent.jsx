import React, { useEffect } from 'react'

import { connect } from 'react-redux'
import ItemComponent from './Cart/ItemComponent'
import TotalComponent from './Cart/TotalComponent'
import { CartComponentPropTypes } from '../cartCommon'

function CartComponent ({ cartItems, purchaseUrl, currency }) {
  const referenceIds = Object.keys(cartItems)

  useEffect(() => {
    if (referenceIds.length === 0) {
      window.location.href = '/'
    }
  }, [cartItems])

  return <table className="table table-hover table-borderless">
    <tbody>
    {
      referenceIds.map(referenceId => <ItemComponent item={cartItems[referenceId]} key={referenceId}
                                                     currency={currency}/>)
    }
    </tbody>

    <TotalComponent cartItems={cartItems} currency={currency} purchaseUrl={purchaseUrl}/>
  </table>
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

export default connect(mapStateToProps, mapDispatchToProps)(CartComponent)
