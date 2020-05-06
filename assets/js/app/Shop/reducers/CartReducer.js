import cartService from "../services/CartService"

export default (state = {}, action) => {
    switch (action.type) {
        case 'IS_LOADING':
            return cartService.itemInLoading(action.reference, action.isLoading).getData()
        case 'UPDATE_QUANTITY':
            return cartService.updateQuantity(action.reference, action.quantity).getData()
        case 'DELETE_ITEM':
            return cartService.deleteItem(action.reference).getData()
        case 'UPDATE_ITEM_QUANTITY':
            return cartService.updateItemQuantity(action.reference, action.quantity).getData()
        case 'ADD_IN_CART':
            return cartService.addInCart(action.reference, action.quantity).getData()
        default:
            return cartService.getData()
    }
}