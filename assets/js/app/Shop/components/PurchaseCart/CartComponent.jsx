import React from 'react'
import { hot } from 'react-hot-loader/root'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import ItemComponent from './Cart/ItemComponent'
import TotalComponent from './Cart/TotalComponent'

class CartComponent extends React.Component {


  getItem (referenceId) {
    return this.props.items[referenceId]
  }

  render () {
    const referenceIds = Object.keys(this.props.items)

    return (
      <table className='table table-hover table-borderless'>
        <tbody>
        {
          referenceIds.map(referenceId =>
            <ItemComponent
              item={this.getItem(referenceId)} key={referenceId}
              currency={this.props.currency}
            />
          )
        }
        </tbody>

        <TotalComponent
          items={this.props.items} currency={this.props.currency}
          purchaseUrl={this.props.purchaseUrl}
        />
      </table>
    )
  }
}

CartComponent.propTypes = {
  items: PropTypes.objectOf(PropTypes.shape({
    quantity: PropTypes.number.isRequired,

    reference: PropTypes.shape({
      url: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      unitPriceIncludingTaxes: PropTypes.number.isRequired,
      unitPriceExcludingTaxes: PropTypes.number.isRequired,
      mainImage: PropTypes.shape({
        urlThumbnail: PropTypes.string.isRequired,
        altThumbnail: PropTypes.string.isRequired
      })
    })
  })).isRequired,
  purchaseUrl: PropTypes.string.isRequired,
  currency: PropTypes.string.isRequired
}

const mapStateToProps = (state) => {
  return {
    items: state.cart.items,
    purchaseUrl: state.cart.purchaseUrl,
    currency: state.cart.currency
  }
}

const mapDispatchToProps = (dispatch) => {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(hot(CartComponent))
