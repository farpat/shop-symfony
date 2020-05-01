import Arr from "../../src/Array/Arr"

class CategoryService {
    constructor() {
        this.baseUrl = window.location.origin + window.location.pathname
        this.data = {}
        this.currentQueryString = ''
    }

    getData() {
        return this.data
    }

    getFilteredProducts(allProducts, currentFilters) {
        return allProducts.filter(product => this.filterProduct(product, currentFilters))
    }

    /**
     *
     * @param {HTMLElement} productsElement
     * @param {HTMLElement} productFieldsElement
     */
    loadData(productsElement, productFieldsElement) {
        let {products, currentPage, perPage, currency} = productsElement.dataset
        currentPage = Number.parseInt(currentPage)
        products = JSON.parse(products)
        perPage = Number.parseInt(perPage)

        const currentFilters = this.getCurrentFiltersFromUrl()
        const currentProducts = this.getFilteredProducts(products, currentFilters)

        this.data = {
            allProducts: products,
            allProductFields: productFieldsElement ? JSON.parse(productFieldsElement.dataset.productFields) : null,
            perPage,
            currency,
            currentFilters,
            currentProducts,
            currentPage: this.ensureCurrentPage(currentPage, currentProducts)
        }

        if (this.data.currentPage !== currentPage) {
            this.refreshUrl()
        }
    }


    /**
     *
     * @param {string} key
     * @param {string} value
     */
    addQueryString(key, value) {
        const prefix = this.currentQueryString.length === 0 ? '?' : '&'
        this.currentQueryString += `${prefix + key}=${value}`
    }

    refreshUrl() {
        this.currentQueryString = ''

        for (const filterKey in this.data.currentFilters) {
            this.addQueryString(filterKey, this.data.currentFilters[filterKey])
        }

        if (this.data.currentPage > 1) {
            this.addQueryString('page', this.data.currentPage)
        }

        window.history.replaceState({}, '', this.baseUrl + this.currentQueryString)
    }

    /**
     *
     * @param {number} newCurrentPage
     * @returns {CategoryService}
     */
    updatePage(newCurrentPage) {
        this.data = {...this.data, currentPage: newCurrentPage}

        this.refreshUrl()

        return this
    }

    ensureCurrentPage(currentPage, currentProducts) {
        let newCurrentPage = currentPage

        const lastPage = Math.ceil(currentProducts.length / this.data.perPage)

        if (lastPage === 0) {
            newCurrentPage = 1
        } else if (lastPage < currentPage) {
            newCurrentPage = lastPage
        }

        return newCurrentPage
    }

    updateFilter(filterKey, newValue) {
        let currentFilters

        if (newValue !== '') {
            currentFilters = {...this.data.currentFilters, [filterKey]: newValue}
        } else {
            currentFilters = {...this.data.currentFilters}
            delete currentFilters[filterKey]
        }

        const currentProducts = this.getFilteredProducts(this.data.allProducts, currentFilters)

        this.data = {
            ...this.data,
            currentPage: this.ensureCurrentPage(this.data.currentPage, currentProducts),
            currentProducts,
            currentFilters
        }

        this.refreshUrl()

        return this
    }

    filterProduct(product, currentFilters) {
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

    getCurrentFiltersFromUrl() {
        let currentFilters = {}

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
