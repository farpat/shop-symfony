import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import HeadCartComponent from './Shop/components/HeadCart/CartComponent'
import ProductComponent from './Shop/components/ProductShow/ProductComponent'
import productAndCartStore from './Shop/services/ProductAndCartStore'
import Arr from '../src/Array/Arr'

// ---- DON'T CLOSE HEADER CART -----\\
const cartNavElement = document.querySelector('#cart-nav')
cartNavElement.addEventListener('click', event => {
  console.log(event)

  if (event.target.id !== 'button-cart' && (event.target.tagName !== 'I' && event.target.tagName !== 'BUTTON')) {
    event.stopPropagation()
  }
})

render(
  <Provider store={productAndCartStore}>
    <HeadCartComponent />
  </Provider>,
  cartNavElement
)

if (!Arr.isEmpty(productAndCartStore.getState().product)) {
  render(
    <Provider store={productAndCartStore}>
      <ProductComponent />
    </Provider>,
    document.querySelector('#product-component')
  )
}
