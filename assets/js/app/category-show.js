import React from "react"
import {render} from "react-dom"
import {Provider} from "react-redux"
import {createStore} from "redux"
import ProductFieldsComponent from "./Category/components/ProductField/ProductFieldsComponent"
import ProductsComponent from "./Category/components/Product/ProductsComponent"
import categoryReducer from "./Category/reducers/categoryReducer"
import categoryService from "./Category/CategoryService"


const productsElement = document.querySelector('#products-component')
if (productsElement) {
    const productFieldsElement = document.querySelector('#product-fields-component')

    categoryService.loadData(productsElement, productFieldsElement)
    const store = createStore(categoryReducer, categoryService.getData())

    render(<Provider store={store}><ProductsComponent/></Provider>, productsElement)

    if (categoryService.getData().allProductFields.length > 0) {
        render(<Provider store={store}><ProductFieldsComponent/></Provider>, productFieldsElement)
    }
}









