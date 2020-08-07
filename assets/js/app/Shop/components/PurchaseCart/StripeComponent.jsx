import React, { useEffect, useState } from 'react'
import { hot } from 'react-hot-loader/root'
import { connect } from 'react-redux'
import CartService from '../../services/CartService'
import Str from '../../../../src/Str'
import Translation from '../../../../src/Translation'
import PropTypes from 'prop-types'
import { jsonPost } from '@farpat/api'

function StripeComponent ({ cartItems, currency, publicKey, successUrl }) {
  const [isPaying, setIsPaying] = useState(false)
  const [error, setError] = useState('')

  const stripe = window.Stripe(publicKey)
  const stripeElements = stripe.elements()
  const stripeCard = stripeElements.create('card', {
    style: { base: { color: '#32325d' } }
  })

  useEffect(() => {
    stripeCard.mount('#card-element')
    stripeCard.on('change', ({ error }) => setError(error ? error.message : ''))
  }, [])

  const onPay = async function (event) {
    if (isPaying) {
      return
    }

    event.preventDefault()

    setIsPaying(true)
    const response = await jsonPost('/purchase/create-intent')
    const result = await stripe.confirmCardPayment(response.client_secret, {
      payment_method: {
        card           : stripeCard,
        billing_details: { name: response.customer_name, address: response.customer_billing_address }
      }
    })

    setIsPaying(false)

    if (result.error) {
      setError(result.error.message)
    } else {
      if (result.paymentIntent.status === 'succeeded') {
        window.location.href = `${successUrl}/${result.paymentIntent.id}`
      }
    }
  }

  const getButtonClassname = function () {
    let className = 'btn btn-primary'

    if (isPaying) {
      className += ' paying'
    }

    return className
  }

  const getErrorClassName = function () {
    let className = 'invalid-feedback'

    if (error) {
      className += ' d-block'
    }

    return className
  }

  const { totalPriceIncludingTaxes } = CartService.getPrices(cartItems)

  return <form id="payment-form" onSubmit={onPay}>
    <div className="form-group">
      <div id="card-element"></div>

      <div className={getErrorClassName()} role="alert">{error}</div>
    </div>

    <button className={getButtonClassname()} disabled={isPaying}>
      {Translation.get('Pay')} {Str.toLocaleCurrency(totalPriceIncludingTaxes, currency)}
    </button>
  </form>
}

StripeComponent.propTypes = {
  cartItems : PropTypes.objectOf(PropTypes.shape({
    quantity: PropTypes.number.isRequired,

    reference: PropTypes.shape({
      unitPriceIncludingTaxes: PropTypes.number.isRequired,
      unitPriceExcludingTaxes: PropTypes.number.isRequired,
    })
  })).isRequired,
  currency  : PropTypes.string.isRequired,
  publicKey : PropTypes.string.isRequired,
  successUrl: PropTypes.string.isRequired,
}

const mapStateToProps = (state) => {
  return {
    cartItems: state.cart.cartItems,
    currency : state.cart.currency,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(hot(StripeComponent))
