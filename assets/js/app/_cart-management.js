import {render} from "react-dom"
import {Provider} from "react-redux"
import {createStore} from "redux"
import cartService from "./Cart/CartService"
import cartReducer from "./Cart/reducer/cartReducer"
import HeadCartComponent from "./Cart/HeadCartComponent"
import React from "react"

const headCartElement = document.querySelector('#cart-nav')
cartService.loadData(headCartElement)

render(
    <Provider store={createStore(cartReducer, cartService.getData())}>
        <HeadCartComponent/>
    </Provider>,
    headCartElement
)