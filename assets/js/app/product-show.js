import React from "react"
import {render} from "react-dom"
import {Provider} from "react-redux"
import {combineReducers, createStore} from "redux"
import ProductComponent from "./Product/components/ProductComponent"
import productService from "./Product/ProductService"
import productReducer from "./Product/reducer/productReducer"
import cartReducer from "./Cart/reducer/cartReducer"

const productElement = document.querySelector('#product-component')
productService.loadData(productElement)

render(
    <Provider store={createStore(combineReducers({productReducer, cartReducer}), {
        productReducer: productService.getData(),
        cartReducer: {}
    })}>
        <ProductComponent/>
    </Provider>,
    productElement
)
