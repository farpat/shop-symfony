import React from 'react'
import PropTypes from 'prop-types'
import Str from '../../../../../src/Str'
import Translation from '../../../../../src/Translation'
import CartService from '../../../services/CartService'
import { TotalComponentPropTypes } from '../../cartCommon'

function TotalComponent ({ cartItems, purchaseUrl, currency }) {
  const { totalPriceExcludingTaxes, totalPriceIncludingTaxes, totalIncludingTaxes } = CartService.getPrices(cartItems)

  return (
    <tfoot className='header-cart-total'>
    <tr>
      <td colSpan='2'>{Translation.get('Subtotal')}:</td>
      <td colSpan='2'>{Str.toLocaleCurrency(totalPriceExcludingTaxes, currency)}</td>
    </tr>
    <tr className='header-cart-total-vat'>
      <td colSpan='2'>{Translation.get('Tax total')}:</td>
      <td colSpan='2'>+ {Str.toLocaleCurrency(totalIncludingTaxes, currency)}</td>
    </tr>
    <tr className='header-cart-total'>
      <td colSpan='2'>{Translation.get('Total')}:</td>
      <td colSpan='2'>{Str.toLocaleCurrency(totalPriceIncludingTaxes, currency)}</td>
    </tr>
    <tr>
      <td colSpan='4'>
        <a className='btn btn-primary' href={purchaseUrl}>{Translation.get('Purchase')}</a>
      </td>
    </tr>
    </tfoot>
  )
}

TotalComponent.propTypes = TotalComponentPropTypes

export default TotalComponent
