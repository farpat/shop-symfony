import React from 'react'
import { hot } from 'react-hot-loader/root'
import { connect } from 'react-redux'
import CartService from '../../services/CartService'
import Str from '../../../../src/String/Str'
import Translation from '../../../../src/Translation/Translation'
import PropTypes from 'prop-types'
import Requestor from '@farpat/api'

class StripeComponent extends React.Component {
  constructor (props) {
    super(props)

    this.state = {
      isPaying: false,
      error   : ''
    }

    this.stripe = window.Stripe(this.props.publicKey)
    this.elements = this.stripe.elements()
    this.card = this.elements.create('card', {
      style: {
        base: { color: '#32325d' }
      }
    })
  }

  async onPay (event) {
    if (this.state.isPaying) {
      return
    }

    event.preventDefault()

    this.setState({ isPaying: true })

    const response = await Requestor.newRequest().post('/purchase/create-intent')

    const result = await this.stripe.confirmCardPayment(response.client_secret, {
      payment_method: {
        card           : this.card,
        billing_details: {
          name   : response.customer_name,
          address: response.customer_billing_address,
        }
      }
    })

    this.setState({ isPaying: false })

    if (result.error) {
      this.setState({ error: result.error.message })
    } else {
      if (result.paymentIntent.status === 'succeeded') {
        window.location.href = `${this.props.successUrl}/${result.paymentIntent.id}`
      }
    }
  }

  componentDidMount () {
    this.card.mount('#card-element')

    this.card.on('change', ({ error }) => {
      this.setState({ error: error ? error.message : '' })
    })
  }

  getButtonClassname () {
    let className = 'btn btn-primary'

    if (this.state.isPaying) {
      className += ' paying'
    }

    return className
  }

  getErrorClassName () {
    let className = 'invalid-feedback'

    if (this.state.error) {
      className += ' d-block'
    }

    return className
  }

  render () {
    const { totalPriceIncludingTaxes } = CartService.getPrices(this.props.items)

    return (
      <div>
        <form id="payment-form" onSubmit={this.onPay.bind(this)}>
          <div className="form-group">
            <div id="card-element"></div>

            <div className={this.getErrorClassName()} role="alert">{this.state.error}</div>
          </div>


          <button className={this.getButtonClassname()} disabled={this.state.isPaying}>
            {Translation.get('Pay')} {Str.toLocaleCurrency(totalPriceIncludingTaxes, this.props.currency)}
          </button>
        </form>
      </div>
    )
  }
}

StripeComponent.propTypes = {
  items     : PropTypes.objectOf(PropTypes.shape({
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
    items   : state.cart.items,
    currency: state.cart.currency,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(hot(StripeComponent))
