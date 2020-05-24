import React from 'react'
import { hot } from 'react-hot-loader/root'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import ItemComponent from './Cart/ItemComponent'
import TotalComponent from './Cart/TotalComponent'

function CartComponent ({ items, purchaseUrl, currency }) {
  const referenceIds = Object.keys(items)

  return (
    <div>
      {
        referenceIds.length === 0 ?
          <div className="nav-link"><i className="fas fa-shopping-cart"></i></div> :
          <div>
            <button
              aria-expanded='false' aria-haspopup='true'
              className='nav-link btn btn-link dropdown-toggle mr-md-2'
              data-toggle='dropdown' id='button-cart'
            >
              <i className="fas fa-shopping-cart"/> - {referenceIds.length}
            </button>
            <div aria-labelledby='button-cart' className='dropdown-menu dropdown-menu-right header-cart'>
              <table className='table table-borderless table-hover'>
                <tbody>
                {
                  referenceIds.map(referenceId =>
                    <ItemComponent item={items[referenceId]} key={referenceId} currency={currency}/>
                  )
                }
                </tbody>

                <TotalComponent items={items} currency={currency} purchaseUrl={purchaseUrl}/>
              </table>
            </div>
          </div>
      }
    </div>
  )
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
