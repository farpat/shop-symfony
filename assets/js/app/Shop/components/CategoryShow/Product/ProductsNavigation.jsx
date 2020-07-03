import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import range from 'lodash/range'
import Translation from '../../../../../src/Translation'

function ProductsNavigation ({ goTo, products, perPage, currentPage }) {
  const getPages = function () {
    const pagesCount = Math.ceil(products.length / perPage)
    if (pagesCount === 0) {
      return []
    }
    return range(1, pagesCount + 1)
  }

  const pages = getPages()

  const getNavClass = function (pages) {
    if (pages.length === 0) {
      return 'd-none'
    }

    return 'mt-2'
  }

  const getPreviousItemClass = function () {
    let className = 'page-item'
    if (currentPage === 1) {
      className += ' disabled'
    }

    return className
  }

  const getNextItemClass = function () {
    let className = 'page-item'
    if (currentPage === pages.length) {
      className += ' disabled'
    }

    return className
  }

  const getItemClass = function (page) {
    let className = 'page-item'
    if (currentPage === page) {
      className += ' active'
    }

    return className
  }

  const handleGoToPage = function (event, page) {
    event.preventDefault()
    goTo(page)
  }

  return (
    <nav aria-label="Product pagination" className={getNavClass(pages)}>
      <ul className="pagination">
        <li className={getPreviousItemClass()}>
          <a href="#" onClick={(event) => handleGoToPage(event, currentPage - 1)} className='page-link'>
            &larr; {Translation.get('previous')}
          </a>
        </li>
        {
          pages.map(page =>
            <li className={getItemClass(page)} key={page}>
              <a href='#' onClick={(event) => handleGoToPage(event, page)} className='page-link'>
                {page}
              </a>
            </li>)
        }
        <li className={getNextItemClass()}>
          <a href="#" onClick={(event) => handleGoToPage(event, currentPage + 1)} className='page-link'>
            {Translation.get('next')} &rarr;
          </a>
        </li>
      </ul>
    </nav>
  )
}

ProductsNavigation.propTypes = {
  products   : PropTypes.arrayOf(PropTypes.shape({
    id     : PropTypes.number.isRequired,
    url    : PropTypes.string.isRequired,
    excerpt: PropTypes.string,
    label  : PropTypes.string.isRequired,
    image  : PropTypes.shape({
      urlThumbnail: PropTypes.string.isRequired
    })
  })),
  perPage    : PropTypes.number.isRequired,
  currentPage: PropTypes.number.isRequired,

  goTo: PropTypes.func.isRequired
}

const mapStateToProps = (state) => {
  return {
    products   : state.currentProducts,
    perPage    : state.perPage,
    currentPage: state.currentPage
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    goTo: (page) => dispatch({ type: 'UPDATE_PAGE', page })
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(ProductsNavigation)
