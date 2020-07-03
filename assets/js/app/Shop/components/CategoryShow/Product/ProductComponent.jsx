import React from 'react'
import { connect } from 'react-redux'
import PropTypes from 'prop-types'
import Str from '../../../../../src/Str'
import Translation from '../../../../../src/Translation'

function ProductComponent ({ product, currency, columns }) {
  const getWrapperClassName = function () {
    return `col-md-${columns} mb-3`
  }

  return (
    <div className={getWrapperClassName()}>
      <article className='card product'>
        {
          product.image &&
          <a href={product.url}>
            <img src={product.image.urlThumbnail} alt={product.image.altThumbnail} className='card-img-top'/>
          </a>
        }
        <div className='card-body'>
          <h3 className='card-title'><a href={product.url}>{product.label}</a></h3>
          <div className='card-text'>
            {product.excerpt}

            <p className='mt-2 m-0'>
              {Translation.get('From')} <span className='badge badge-secondary'>
                {Str.toLocaleCurrency(product.minUnitPriceIncludingTaxes, currency)}
              </span>
            </p>
          </div>
        </div>
      </article>
    </div>
  )
}

ProductComponent.propTypes = {
  product : PropTypes.shape({
    id                        : PropTypes.number.isRequired,
    url                       : PropTypes.string.isRequired,
    excerpt                   : PropTypes.string,
    label                     : PropTypes.string.isRequired,
    minUnitPriceIncludingTaxes: PropTypes.number.isRequired,
    image                     : PropTypes.shape({
      urlThumbnail: PropTypes.string.isRequired,
      altThumbnail: PropTypes.string
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
