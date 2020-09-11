import React from 'react'
import { hot } from 'react-hot-loader/root'
import { connect } from 'react-redux'
import PropTypes from 'prop-types'
import ReferenceNavComponent from './Product/ReferenceNavComponent'
import ProductReferenceComponent from './Product/ProductReferenceComponent'

function ProductComponent ({ currency, currentReference }) {

  return (
    <>
      <ReferenceNavComponent/>

      <ProductReferenceComponent currentReference={currentReference} currency={currency}/>
    </>
  )
}

ProductComponent.propTypes = {
  currentReference: PropTypes.shape({
    id                     : PropTypes.number.isRequired,
    label                  : PropTypes.string.isRequired,
    main_image             : PropTypes.shape({
      url_thumbnail: PropTypes.string.isRequired,
      alt_thumbnail: PropTypes.string.isRequired
    }),
    unit_price_including_taxes: PropTypes.number.isRequired
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
