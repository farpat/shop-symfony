import React from "react"
import {render} from "react-dom"
import {Provider} from "react-redux"
import ProductsComponent from "./Shop/components/CategoryShow/ProductsComponent"
import ProductFieldsComponent from "./Shop/components/CategoryShow/ProductFieldsComponent"
import categoryService from "./Shop/services/CategoryService"

const productsElement = document.querySelector('#products-component')
const productFieldsElement = document.querySelector('#product-fields-component')

const categoryStore = categoryService.createInitialData(productsElement, productFieldsElement)

render(<Provider store={categoryStore}><ProductsComponent/></Provider>, productsElement)

if (categoryService.getData().allProductFields.length > 0) {
    render(<Provider store={categoryStore}><ProductFieldsComponent/></Provider>, productFieldsElement)
}
