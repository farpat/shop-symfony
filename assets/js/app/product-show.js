import productAndCartStore from './Shop/services/ProductAndCartStore'
import ProductComponent from './Shop/components/ProductShow/ProductComponent'
import { render } from 'react-dom'
import React from 'react'
import { Provider } from 'react-redux'

render(
  <Provider store={productAndCartStore}>
    <ProductComponent/>
  </Provider>,
  document.querySelector('#product-component')
)