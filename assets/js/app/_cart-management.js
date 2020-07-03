import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import HeadCartComponent from './Shop/components/HeadCart/CartComponent'
import PurchaseCartComponent from './Shop/components/PurchaseCart/CartComponent'
import ProductComponent from './Shop/components/ProductShow/ProductComponent'
import productAndCartStore from './Shop/services/ProductAndCartStore'
import StripeComponent from './Shop/components/PurchaseCart/StripeComponent'
import Dom from '../src/Dom'

const cartNavElement = document.querySelector('#cart-nav')

//Don't close header cart when we click inside it
const cartNav = document.querySelector('#cart-nav')
$(cartNav).on('hide.bs.dropdown', function (event) {
  if (event.clickEvent && Dom.isInside(cartNav, event.clickEvent.originalEvent.target)) {
    event.preventDefault()
  }
})

//Header cart component (in navbar)
render(
  <Provider store={productAndCartStore}>
    <HeadCartComponent/>
  </Provider>,
  cartNavElement
)
