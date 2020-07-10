import productService from '../services/ProductService'

export default (state = {}, action) => {
  switch (action.type) {
    case 'UPDATE_REFERENCE':
      return productService.updateReference(action.reference).getData()
    default:
      return productService.getData()
  }
}
