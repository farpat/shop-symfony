import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import range from 'lodash/range'
import Translation from '../../../../../src/Translation'

const getPages = function (products, perPage) {
  const pagesCount = Math.ceil(products.length / perPage)
  if (pagesCount === 0) {
    return []
  }
  return range(1, pagesCount + 1)
}

function ProductsNavigation ({ goTo, products, perPage, currentPage }) {
  const pages = getPages(products, perPage)
  const currentUrl = window.location.href

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
    if (page !== currentPage) {
      goTo(page)
    }
  }

  const getHref = function (page) {
    const regex = /&page=\d+$/

    if (page > 1) {
      const queryString = `&page=${page}`
      return regex.test(currentUrl) ? currentUrl.replace(regex, queryString) : `${currentUrl}${queryString}`
    } else {
      return currentUrl.replace(regex, '')
    }
  }

  if (pages.length === 0) {
    return null
  }

  return <nav aria-label="Product pagination">
    <ul className="pagination">
      <li className={getPreviousItemClass()}>
        <a href={getHref(currentPage - 1)} onClick={(event) => handleGoToPage(event, currentPage - 1)}
           className='page-link'>
          &larr; {Translation.get('previous')}
        </a>
      </li>
      {
        pages.map(page =>
          <li className={getItemClass(page)} key={page}>
            <a href={getHref(page)} onClick={(event) => handleGoToPage(event, page)} className='page-link'>
              {page}
            </a>
          </li>)
      }
      <li className={getNextItemClass()}>
        <a href={getHref(currentPage + 1)} onClick={(event) => handleGoToPage(event, currentPage + 1)}
           className='page-link'>
          {Translation.get('next')} &rarr;
        </a>
      </li>
    </ul>
  </nav>
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
