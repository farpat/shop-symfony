import cartService from "../CartService"

export default (state = {}, action) => {
    switch (action.type) {
        default:
            return cartService.getData()
    }
}