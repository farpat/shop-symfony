import React from 'react'
import PropTypes from 'prop-types'
import Str from '../../../../../src/Str'
import Translation from '../../../../../src/Translation'
import CartService from '../../../services/CartService'

function TotalComponent ({ items, currency }) {
  const { totalPriceExcludingTaxes, totalPriceIncludingTaxes, totalIncludingTaxes } = CartService.getPrices(items)

  return (
    <tfoot className='header-cart-total'>
    <tr>
      <td colSpan='2'>{Translation.get('Subtotal')}:</td>
      <td colSpan='2'>{Str.toLocaleCurrency(totalPriceExcludingTaxes, currency)}</td>
    </tr>
    <tr className='header-cart-total-vat'>
      <td className='text-right' colSpan='2'>{Translation.get('Tax total')}:</td>
      <td colSpan='2'>+ {Str.toLocaleCurrency(totalIncludingTaxes, currency)}</td>
    </tr>
    <tr className='header-cart-total'>
      <td className='text-right' colSpan='2'>{Translation.get('Total')}:</td>
      <td colSpan='2'>{Str.toLocaleCurrency(totalPriceIncludingTaxes, currency)}</td>
    </tr>
    </tfoot>
  )
}

TotalComponent.propTypes = {
  items   : PropTypes.objectOf(PropTypes.shape({
    quantity : PropTypes.number.isRequired,
    reference: PropTypes.shape({
      unitPriceIncludingTaxes: PropTypes.number.isRequired,
      unitPriceExcludingTaxes: PropTypes.number.isRequired
    })
  })).isRequired,
  currency: PropTypes.string.isRequired
}

export default TotalComponent
