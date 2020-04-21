import Arr from "../../../src/Array/Arr";

/**
 *
 * @param {Array} filters
 * @param {Number} currentPage
 * @param {String} baseUrl
 */
function refreshUrl(filters, currentPage, baseUrl) {
    let currentQueryString = '';

    for (const filterKey in filters) {
        currentQueryString = addQueryString(currentQueryString, filterKey, filters[filterKey]);
    }

    if (currentPage > 1) {
        currentQueryString = addQueryString(currentQueryString, 'page', currentPage);
    }

    window.history.replaceState({}, '', baseUrl + currentQueryString);
}

/**
 *
 * @param {String} currentQueryString
 * @param {String} key
 * @param {String} value
 * @returns {string}
 */
function addQueryString(currentQueryString, key, value) {
    const prefix = currentQueryString.length === 0 ? '?' : '&';
    return currentQueryString + `${prefix + key}=${value}`;
}

/**
 *
 * @param {Object} product
 * @param {Object} filters
 * @returns {boolean}
 */
function filterProduct(product, filters) {
    if (Arr.isEmpty(filters)) {
        return true;
    }

    for (const filterKey in filters) {
        const filterValue = filters[filterKey];
        let matches;
        if ((matches = /(\d+)-max$/.exec(filterKey)) !== null) {
            const filterId = matches[1];
            if (product.references.find(reference => reference.filled_product_fields[filterId] <= filterValue) === undefined) {
                return false;
            }
        } else if ((matches = /(\d+)-min$/.exec(filterKey)) !== null) {
            const filterId = matches[1];
            if (product.references.find(reference => reference.filled_product_fields[filterId] >= filterValue) === undefined) {
                return false;
            }
        } else if ((matches = /-(\d+)$/.exec(filterKey)) !== null) {
            const filterId = matches[1];
            if (product.references.find((reference) => reference.filled_product_fields[filterId].includes(filterValue)) === undefined) {
                return false;
            }
        }
    }

    return true;
}

export default (state = {}, action) => {
    let currentPage, currentFilters;

    switch (action.type) {
        case 'UPDATE_FILTER':
            if (action.value !== '') {
                currentFilters = {...state.currentFilters, [action.key]: action.value};
            } else {
                currentFilters = {...state.currentFilters};
                delete currentFilters[action.key];
            }
            const currentProducts = state.allProducts.filter(product => filterProduct(product, currentFilters));

            currentPage = state.currentPage;
            const lastPage = Math.ceil(currentProducts.length / state.perPage);
            if (lastPage === 0) {
                currentPage = 1;
            } else if (lastPage < state.currentPage) {
                currentPage = lastPage;
            }

            refreshUrl(currentFilters, currentPage, window.location.origin + window.location.pathname);

            return {...state, currentPage, currentProducts, currentFilters};
        case 'UPDATE_PAGE':
            currentPage = action.page;

            refreshUrl(state.currentFilters, currentPage, window.location.origin + window.location.pathname);

            return {...state, currentPage};
        default:
            if (state.allProducts) {
                state = {
                    ...state,
                    currentProducts: state.allProducts.filter(product => filterProduct(product, state.currentFilters)),
                };
            }

            return state;
    }
}