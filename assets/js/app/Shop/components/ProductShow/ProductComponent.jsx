import React from 'react'
import { hot } from 'react-hot-loader/root'
import { connect } from 'react-redux'
import PropTypes from 'prop-types'
import ReferenceNavComponent from './Product/ReferenceNavComponent'
import ProductReferenceComponent from './Product/ProductReferenceComponent'

function ProductComponent ({ currency, currentReference }) {
  return (
    <div>
      <ReferenceNavComponent/>

      <ProductReferenceComponent
        currentReference={currentReference}
        currency={currency}
      />
    </div>
  )
}

ProductComponent.propTypes = {
  currentReference: PropTypes.shape({
    id                     : PropTypes.number.isRequired,
    label                  : PropTypes.string.isRequired,
    mainImage              : PropTypes.shape({
      urlThumbnail: PropTypes.string.isRequired,
      altThumbnail: PropTypes.string.isRequired
    }),
    unitPriceIncludingTaxes: PropTypes.number.isRequired
  }),
  currency        : PropTypes.string.isRequired
}

const mapStateToProps = (state) => {
  return {
    currentReference: state.product.currentReference,
    currency        : state.cart.currency
  }
}

const mapDispatchToProps = (dispatch) => {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(hot(ProductComponent))
