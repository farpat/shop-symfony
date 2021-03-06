import React from 'react'
import { connect } from 'react-redux'
import PropTypes from 'prop-types'
import Str from '../../../../../src/Str'
import Translation from '../../../../../src/Translation'

function ProductComponent ({ product, currency, columns }) {
  return <article className='product'>
    {
      product.image &&
      <a href={product.url}>
        <img src={product.image.url_thumbnail} alt={product.image.alt_thumbnail} className='product-img-top'/>
      </a>
    }
    <div className='product-body'>
      <h3 className='product-title'><a href={product.url}>{product.label}</a></h3>
      <div className='product-text'>
        {product.excerpt}

        <p className="product-price">
          {Translation.get('From')} <span className='badge bg-secondary'>
                {Str.toLocaleCurrency(product.min_unit_price_including_taxes, currency)}
              </span>
        </p>
      </div>
    </div>
  </article>
}

ProductComponent.propTypes = {
  product : PropTypes.shape({
    id                            : PropTypes.number.isRequired,
    url                           : PropTypes.string.isRequired,
    excerpt                       : PropTypes.string,
    label                         : PropTypes.string.isRequired,
    min_unit_price_including_taxes: PropTypes.number.isRequired,
    image                         : PropTypes.shape({
      url_thumbnail: PropTypes.string.isRequired,
      alt_thumbnail: PropTypes.string
    })
  }),
  currency: PropTypes.string.isRequired,
  columns : PropTypes.number.isRequired,
}

const mapStateToProps = (state) => {
  return {
    currency: state.currency,
    columns : state.columns
  }
}

const mapDispatchToProps = (dispatch) => {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(ProductComponent)
