import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import ProductsComponent from './Shop/components/CategoryShow/ProductsComponent'
import ProductFieldsComponent from './Shop/components/CategoryShow/ProductFieldsComponent'
import categoryService from './Shop/services/CategoryService'
import { createStore } from 'redux'
import CategoryReducer from './Shop/reducers/CategoryReducer'

const productsElement = document.querySelector('#products-component')
const productFieldsElement = document.querySelector('#product-fields-component')

const categoryStore = createStore(CategoryReducer,
  categoryService.setInitialData(productsElement, productFieldsElement).getData()
)

render(<Provider store={categoryStore}>
  <ProductsComponent/>
</Provider>, productsElement)

if (productFieldsElement) {
  render(<Provider store={categoryStore}>
    <ProductFieldsComponent/>
  </Provider>, productFieldsElement)
}
