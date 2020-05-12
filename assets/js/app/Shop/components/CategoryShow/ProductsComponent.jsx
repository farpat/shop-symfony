import React from 'react'
import PropTypes from 'prop-types'
import { hot } from 'react-hot-loader/root'
import { connect } from 'react-redux'
import ProductComponent from './Product/ProductComponent'
import ProductsNavigation from './Product/ProductsNavigation'

class ProductsComponent extends React.Component {
  constructor (props) {
    super(props)
  }

  getProductsToDisplay () {
    const start = (this.props.currentPage - 1) * this.props.perPage
    return this.props.products.slice(start, start + this.props.perPage)
  }

  render () {
    const productsToDisplay = this.getProductsToDisplay()

    return (
      <div className='products-component'>
        <ProductsNavigation />
        <div className='row'>
          {
            productsToDisplay.map(product => <ProductComponent key={product.id} product={product} />)
          }
          {
            productsToDisplay.length === 0 &&
              <p>Sorry! There are no products</p>
          }
        </div>
      </div>
    )
  }
}

ProductsComponent.propTypes = {
  products: PropTypes.arrayOf(PropTypes.shape({
    id: PropTypes.number.isRequired,
    url: PropTypes.string.isRequired,
    excerpt: PropTypes.string,
    label: PropTypes.string.isRequired,
    minUnitPriceIncludingTaxes: PropTypes.number.isRequired,
    image: PropTypes.shape({
      urlThumbnail: PropTypes.string.isRequired,
      altThumbnail: PropTypes.string
    })
  })),
  currentPage: PropTypes.number.isRequired,
  perPage: PropTypes.number.isRequired
}

const mapStateToProps = (state) => {
  return {
    products: state.currentProducts,
    currentPage: state.currentPage,
    perPage: state.perPage
  }
}

const mapDispatchToProps = (dispatch) => {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(hot(ProductsComponent))
