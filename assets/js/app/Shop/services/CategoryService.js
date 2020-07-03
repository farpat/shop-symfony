import Arr from '../../../src/Arr'
import Str from '../../../src/Str'

class CategoryService {
  constructor () {
    this.baseUrl = window.location.origin + window.location.pathname
    this.data = {}
  }

  getData () {
    return this.data
  }

  /**
   * @private
   * @param allProducts
   * @param currentFilters
   * @returns {Array}
   */
  getFilteredProducts (allProducts, currentFilters) {
    return allProducts.filter(product => this.filterProduct(product, currentFilters))
  }

  /**
   *
   * @param {HTMLElement} productsElement
   * @param {HTMLElement} productFieldsElement
   */
  setInitialData (productsElement, productFieldsElement) {
    let { products, currentPage, perPage, columns } = productsElement.dataset
    currentPage = Number.parseInt(currentPage)
    products = JSON.parse(products)
    perPage = Number.parseInt(perPage)
    columns = Number.parseInt(columns)

    const currentFilters = this.getCurrentFiltersFromUrl()
    const currentProducts = this.getFilteredProducts(products, currentFilters)

    this.data = {
      allProducts     : products,
      allProductFields: productFieldsElement ? JSON.parse(productFieldsElement.dataset.productFields) : null,
      perPage,
      columns,
      currency        : document.querySelector('#cart-nav').dataset.currency,
      currentFilters,
      currentProducts,
      currentPage     : this.ensureCurrentPage(currentPage, currentProducts)
    }

    if (this.data.currentPage !== currentPage) {
      this.refreshUrl(this.data.currentFilters, this.data.currentPage)
    }

    return this
  }

  /**
   * @private
   * @param {object} currentFilters
   * @param {number} currentPage
   */
  refreshUrl (currentFilters, currentPage) {
    let currentQueryString = ''

    for (const filterKey in currentFilters) {
      currentQueryString += Str.addQueryString(currentQueryString, filterKey, this.data.currentFilters[filterKey])
    }

    if (currentPage > 1) {
      currentQueryString += Str.addQueryString(currentQueryString, 'page', this.data.currentPage)
    }

    window.history.replaceState({}, '', this.baseUrl + currentQueryString)
  }

  /**
   *
   * @param {number} newCurrentPage
   * @returns {CategoryService}
   */
  updatePage (newCurrentPage) {
    this.data = { ...this.data, currentPage: newCurrentPage }

    this.refreshUrl(this.data.currentFilters, this.data.currentPage)

    return this
  }

  /**
   * @private
   * @param currentPage
   * @param currentProducts
   * @returns {number}
   */
  ensureCurrentPage (currentPage, currentProducts) {
    let newCurrentPage = currentPage

    const lastPage = Math.ceil(currentProducts.length / this.data.perPage)

    if (lastPage === 0) {
      newCurrentPage = 1
    } else if (lastPage < currentPage) {
      newCurrentPage = lastPage
    }

    return newCurrentPage
  }

  /**
   *
   * @param {string} filterKey
   * @param {string} newValue
   * @returns {CategoryService}
   */
  updateFilter (filterKey, newValue) {
    let currentFilters
    if (newValue !== '') {
      currentFilters = { ...this.data.currentFilters, [filterKey]: newValue }
    } else {
      currentFilters = { ...this.data.currentFilters }
      delete currentFilters[filterKey]
    }

    const currentProducts = this.getFilteredProducts(this.data.allProducts, currentFilters)
    const currentPage = this.ensureCurrentPage(this.data.currentPage, currentProducts)

    this.data = {
      ...this.data,
      currentPage,
      currentProducts,
      currentFilters
    }

    this.refreshUrl(currentFilters, currentPage)

    return this
  }

  /**
   * @private
   * @param {Object} product
   * @param {Object} currentFilters
   * @returns {boolean}
   */
  filterProduct (product, currentFilters) {
    if (Arr.isEmpty(currentFilters)) {
      return true
    }

    for (const filterKey in currentFilters) {
      const filterValue = currentFilters[filterKey]
      let matches
      if ((matches = /(\d+)-max$/.exec(filterKey)) !== null) {
        const filterId = matches[1]
        if (product.references.find(reference => reference.filled_product_fields[filterId] <= filterValue) === undefined) {
          return false
        }
      } else if ((matches = /(\d+)-min$/.exec(filterKey)) !== null) {
        const filterId = matches[1]
        if (product.references.find(reference => reference.filled_product_fields[filterId] >= filterValue) === undefined) {
          return false
        }
      } else if ((matches = /-(\d+)$/.exec(filterKey)) !== null) {
        const filterId = matches[1]
        if (product.references.find((reference) => reference.filled_product_fields[filterId].includes(filterValue)) === undefined) {
          return false
        }
      }
    }

    return true
  }

  /**
   * @private
   * @returns {Object}
   */
  getCurrentFiltersFromUrl () {
    const currentFilters = {}

    const urlParams = (new URL(window.location.href)).searchParams
    for (const [filterKey, filterValue] of Array.from(urlParams.entries())) {
      if (filterKey !== 'page') {
        currentFilters[filterKey] = filterValue
      }
    }

    return currentFilters
  }
}

export default new CategoryService()
