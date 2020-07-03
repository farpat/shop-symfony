import React from 'react'
import PropTypes from 'prop-types'
import Str from '../../../../../src/Str'
import Translation from '../../../../../src/Translation'
import CartService from '../../../services/CartService'

function TotalComponent ({ cartItems, purchaseUrl, currency }) {
  const { totalPriceExcludingTaxes, totalPriceIncludingTaxes, totalIncludingTaxes } = CartService.getPrices(cartItems)

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
    <tr>
      <td colSpan='4'>
        <a className='float-right btn btn-primary' href={purchaseUrl}>{Translation.get('Purchase')}</a>
      </td>
    </tr>
    </tfoot>
  )
}

TotalComponent.propTypes = {
  cartItems  : PropTypes.objectOf(PropTypes.shape({
    quantity : PropTypes.number.isRequired,
    reference: PropTypes.shape({
      unitPriceIncludingTaxes: PropTypes.number.isRequired,
      unitPriceExcludingTaxes: PropTypes.number.isRequired
    })
  })).isRequired,
  purchaseUrl: PropTypes.string.isRequired,
  currency   : PropTypes.string.isRequired
}

export default TotalComponent
