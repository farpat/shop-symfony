import React from "react"
import {render} from "react-dom"
import {Provider} from "react-redux"
import HeadCartComponent from "./Shop/components/HeadCart/CartComponent"
import ProductComponent from "./Shop/components/ProductShow/ProductComponent"
import productAndCartStore from "./Shop/services/ProductAndCartStore"
import Arr from "../src/Array/Arr"

render(
    <Provider store={productAndCartStore}>
        <HeadCartComponent/>
    </Provider>,
    document.querySelector('#cart-nav')
)

if (!Arr.isEmpty(productAndCartStore.getState().product)) {
    render(
        <Provider store={productAndCartStore}>
            <ProductComponent/>
        </Provider>,
        document.querySelector('#product-component')
    )
}
