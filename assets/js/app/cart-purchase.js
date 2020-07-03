import productAndCartStore from './Shop/services/ProductAndCartStore'
import PurchaseCartComponent from './Shop/components/PurchaseCart/CartComponent'
import StripeComponent from './Shop/components/PurchaseCart/StripeComponent'
import { render } from 'react-dom'
import { Provider } from 'react-redux'

render(
  <Provider store={productAndCartStore}>
    <PurchaseCartComponent/>
  </Provider>,
  document.querySelector('#purchase-cart')
)

const stripeElement = document.querySelector('#stripe-component')
render(
  <Provider store={productAndCartStore}>
    <StripeComponent publicKey={stripeElement.dataset.publicKey} successUrl={stripeElement.dataset.successUrl}/>
  </Provider>,
  stripeElement)