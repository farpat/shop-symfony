import productService from '../services/ProductService'

export default (state = {}, action) => {
  switch (action.type) {
    case 'CHANGE_REFERENCE_IN_NAV':
      return productService.updateReference(action.reference).getData()
    default:
      return productService.getData()
  }
}
