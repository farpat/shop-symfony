import React, { useEffect } from 'react'
import { hot } from 'react-hot-loader/root'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import ItemComponent from './Cart/ItemComponent'
import TotalComponent from './Cart/TotalComponent'

function CartComponent ({ items, purchaseUrl, currency }) {
  const referenceIds = Object.keys(items)

  useEffect(() => {
    if (referenceIds.length === 0) {
      window.location.href = '/'
    }
  }, [referenceIds])

  return <table className="table table-hover table-borderless">
    <tbody>
    {
      referenceIds.map(referenceId => <ItemComponent item={items[referenceId]} key={referenceId} currency={currency}/>)
    }
    </tbody>

    <TotalComponent items={items} purchaseUrl={purchaseUrl} currency={currency}/>
  </table>
}

CartComponent.propTypes = {
  items      : PropTypes.objectOf(PropTypes.shape({
    quantity: PropTypes.number.isRequired,

    reference: PropTypes.shape({
      url                    : PropTypes.string.isRequired,
      label                  : PropTypes.string.isRequired,
      unitPriceIncludingTaxes: PropTypes.number.isRequired,
      unitPriceExcludingTaxes: PropTypes.number.isRequired,
      mainImage              : PropTypes.shape({
        urlThumbnail: PropTypes.string.isRequired,
        altThumbnail: PropTypes.string.isRequired
      })
    })
  })).isRequired,
  purchaseUrl: PropTypes.string.isRequired,
  currency   : PropTypes.string.isRequired
}

const mapStateToProps = (state) => {
  return {
    items      : state.cart.items,
    purchaseUrl: state.cart.purchaseUrl,
    currency   : state.cart.currency
  }
}

const mapDispatchToProps = (dispatch) => {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(hot(CartComponent))
