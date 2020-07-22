import React from 'react'
import { hot } from 'react-hot-loader/root'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import ItemComponent from './Cart/ItemComponent'
import TotalComponent from './Cart/TotalComponent'

function CartComponent ({ cartItems, purchaseUrl, currency }) {
  const referenceIds = Object.keys(cartItems)

  if (referenceIds.length === 0) {
    return <button className="nav-link nav-link-dropdown">
      <i className="fas fa-shopping-cart text-muted"></i>
    </button>
  }

  return <>
    <button className="nav-link nav-link-dropdown">
      <i className="fas fa-shopping-cart"/> - {referenceIds.length}
    </button>
    <div className='nav-dropdown-items header-cart'>
      <table className='table table-borderless table-hover'>
        <tbody>
        {
          referenceIds.map(referenceId =>
            <ItemComponent item={cartItems[referenceId]} key={referenceId} currency={currency}/>
          )
        }
        </tbody>

        <TotalComponent cartItems={cartItems} currency={currency} purchaseUrl={purchaseUrl}/>
      </table>
    </div>
  </>
}

CartComponent.propTypes = {
  cartItems  : PropTypes.objectOf(PropTypes.shape({
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
    cartItems  : state.cart.cartItems,
    purchaseUrl: state.cart.purchaseUrl,
    currency   : state.cart.currency
  }
}

const mapDispatchToProps = (dispatch) => {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(hot(CartComponent))
