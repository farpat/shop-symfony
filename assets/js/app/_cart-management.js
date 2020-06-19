import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import HeadCartComponent from './Shop/components/HeadCart/CartComponent'
import PurchaseCartComponent from './Shop/components/PurchaseCart/CartComponent'
import ProductComponent from './Shop/components/ProductShow/ProductComponent'
import productAndCartStore from './Shop/services/ProductAndCartStore'
import StripeComponent from './Shop/components/PurchaseCart/StripeComponent'

const cartNavElement = document.querySelector('#cart-nav')

//Don't close header cart when we click inside it
$('#cart-nav').on('hide.bs.dropdown', function (event) {
  const target = event.clickEvent.originalEvent.target
  if (target.tagName === 'I' || target.tagName === 'BUTTON' || target.tagName === 'A') {
    return false
  }
})

//Header cart component (in navbar)
render(
  <Provider store={productAndCartStore}>
    <HeadCartComponent/>
  </Provider>,
  cartNavElement
)

//Mount Product component if we are in "product/show.html.twig"
const productComponentElement = document.querySelector('#product-component')
if (productComponentElement !== null) {
  render(
    <Provider store={productAndCartStore}>
      <ProductComponent/>
    </Provider>,
    productComponentElement
  )
}

//Mount PurchaseCart component and Stripe component if we are in "cart/purchase.html.twig"
const purchaseCartElement = document.querySelector('#purchase-cart')
if (purchaseCartElement !== null) {
  render(
    <Provider store={productAndCartStore}>
      <PurchaseCartComponent/>
    </Provider>,
    purchaseCartElement
  )

  const stripeElement = document.querySelector('#stripe-component')
  render(
    <Provider store={productAndCartStore}>
      <StripeComponent publicKey={stripeElement.dataset.publicKey} successUrl={stripeElement.dataset.successUrl}/>
    </Provider>,
    stripeElement)
}

