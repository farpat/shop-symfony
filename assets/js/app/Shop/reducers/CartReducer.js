import cartService from '../services/CartService'

export default (state = {}, action) => {
  switch (action.type) {
    case 'SET_QUANTITY_IS_LOADING':
      return cartService.setQuantityInLoading(action.reference, action.isLoading).getData()
    case 'SET_CART_ITEM_IS_LOADING':
      return cartService.setCartItemInLoading(action.reference, action.isLoading).getData()
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
