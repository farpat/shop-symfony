import React from 'react'
import { hot } from 'react-hot-loader/root'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import ItemComponent from './Cart/ItemComponent'
import TotalComponent from './Cart/TotalComponent'

function CartComponent ({ cartItems, purchaseUrl, currency }) {
  const referenceIds = Object.keys(cartItems)

  if (referenceIds.length === 0) {
    return <i className="fas fa-shopping-cart text-muted"></i>
  }

  return <div>
    <button
      aria-expanded="false" aria-haspopup="true"
      className="nav-link btn btn-link dropdown-toggle mr-md-2"
      data-toggle="dropdown" id="button-cart"
    >
      <i className="fas fa-shopping-cart"/> - {referenceIds.length}
    </button>
    <div aria-labelledby='button-cart' className='dropdown-menu dropdown-menu-right header-cart'>
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
  </div>
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
