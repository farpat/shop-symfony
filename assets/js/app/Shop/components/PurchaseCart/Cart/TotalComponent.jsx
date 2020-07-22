import React from 'react'
import PropTypes from 'prop-types'
import Str from '../../../../../src/Str'
import Translation from '../../../../../src/Translation'
import CartService from '../../../services/CartService'

function TotalComponent ({ cartItems, currency }) {
  const { totalPriceExcludingTaxes, totalPriceIncludingTaxes, totalIncludingTaxes } = CartService.getPrices(cartItems)

  return (
    <tfoot className='purchase-cart-total'>
    <tr>
      <td colSpan='2'>{Translation.get('Subtotal')}:</td>
      <td colSpan='2'>{Str.toLocaleCurrency(totalPriceExcludingTaxes, currency)}</td>
    </tr>
    <tr className='purchase-cart-total-vat'>
      <td colSpan='2'>{Translation.get('Tax total')}:</td>
      <td colSpan='2'>+ {Str.toLocaleCurrency(totalIncludingTaxes, currency)}</td>
    </tr>
    <tr className='purchase-cart-total'>
      <td colSpan='2'>{Translation.get('Total')}:</td>
      <td colSpan='2'>{Str.toLocaleCurrency(totalPriceIncludingTaxes, currency)}</td>
    </tr>
    </tfoot>
  )
}

TotalComponent.propTypes = {
  cartItems   : PropTypes.objectOf(PropTypes.shape({
    quantity : PropTypes.number.isRequired,
    reference: PropTypes.shape({
      unitPriceIncludingTaxes: PropTypes.number.isRequired,
      unitPriceExcludingTaxes: PropTypes.number.isRequired
    })
  })).isRequired,
  currency: PropTypes.string.isRequired
}

export default TotalComponent
