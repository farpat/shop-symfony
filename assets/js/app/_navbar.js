import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import productAndCartStore from './Shop/services/ProductAndCartStore'
import HeadCartComponent from './Shop/components/HeadCart/CartComponent'

//Header cart component (in navbar)
const cartNavElement = document.querySelector('#cart-nav')
render(
  <Provider store={productAndCartStore}>
    <HeadCartComponent />
  </Provider>,
  cartNavElement
)

const toggleButton = document.querySelector('#navbar-toggle')
toggleButton.addEventListener('click', function () {
  toggleButton.parentElement.nextElementSibling.classList.toggle('selected')
})

const dropdowns = document.querySelectorAll('.dropdown > a')

dropdowns.forEach(dropdown => {
  dropdown.addEventListener('click', function (event) {
    // if (window.innerWidth < 576) {
    event.preventDefault()
    dropdown.nextElementSibling.classList.toggle('selected')
    // }
  })
})