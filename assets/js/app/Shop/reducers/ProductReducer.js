import productService from "../services/ProductService"

export default (state = {}, action) => {
    switch (action.type) {
        case 'UPDATE_ACTIVATED_SLIDER_INDEX_BY_REFERENCE':
            return productService.updateActivatedSliderIndexByReference(action.reference, action.index).getData()
        case 'UPDATE_REFERENCE':
            return productService.updateReference(action.reference).getData()
        default:
            return productService.getData()
    }
}