import {combineReducers, createStore} from "redux"
import CartReducer from "../reducers/CartReducer"
import ProductReducer from "../reducers/ProductReducer"
import CartService from "./CartService"
import ProductService from "./ProductService"

export default createStore(combineReducers({cart: CartReducer, product: ProductReducer}, {
    cart:    CartService.createInitialData(document.querySelector('#cart-nav')),
    product: ProductService.createInitialData(document.querySelector('#product-component'))
}))
