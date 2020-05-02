import cartService from "../services/CartService"

export default (state = {}, action) => {
    switch (action.type) {
        case 'UPDATE_QUANTITY':
            return cartService.updateQuantity(action.reference, action.quantity).getData()
        case 'ADD_IN_CART':
            return cartService.addInCart(action.reference, action.quantity).getData()
        default:
            return cartService.getData()
    }
}