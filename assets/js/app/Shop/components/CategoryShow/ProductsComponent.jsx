import React from 'react'
import PropTypes from 'prop-types'

import { connect } from 'react-redux'
import ProductComponent from './Product/ProductComponent'
import ProductsNavigation from './Product/ProductsNavigation'
import Translation from '../../../../src/Translation'

function ProductsComponent ({ products, currentPage, perPage }) {
  const getProductsToDisplay = function () {
    const start = (currentPage - 1) * perPage
    return products.slice(start, start + perPage)
  }

  const productsToDisplay = getProductsToDisplay()

  return (
    <div className="products-components">
      <ProductsNavigation/>
      <div className="products-category">
        {
          productsToDisplay.length === 0 ?
            <p>{Translation.get('Sorry! There are no products')}</p> :
            productsToDisplay.map(product => <ProductComponent key={product.id} product={product}/>)
        }
      </div>
    </div>
  )
}

ProductsComponent.propTypes = {
  products   : PropTypes.arrayOf(PropTypes.shape({
    id                            : PropTypes.number.isRequired,
    url                           : PropTypes.string.isRequired,
    excerpt                       : PropTypes.string,
    label                         : PropTypes.string.isRequired,
    min_unit_price_including_taxes: PropTypes.number.isRequired,
    image                         : PropTypes.shape({
      url_thumbnail: PropTypes.string.isRequired,
      alt_thumbnail: PropTypes.string
    })
  })),
  currentPage: PropTypes.number.isRequired,
  perPage    : PropTypes.number.isRequired,
}

const mapStateToProps = (state) => {
  return {
    products   : state.currentProducts,
    currentPage: state.currentPage,
    perPage    : state.perPage
  }
}

const mapDispatchToProps = (dispatch) => {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(ProductsComponent)
