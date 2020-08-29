import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import productAndCartStore from './Shop/services/ProductAndCartStore'
import HeadCartComponent from './Shop/components/HeadCart/CartComponent'

//Header cart component (in navbar)
const cartNavElement = document.querySelector('#cart-nav')
render(
  <Provider store={productAndCartStore}><HeadCartComponent/></Provider>,
  cartNavElement
)

//in little screen, to display menu
const toggleButton = document.querySelector('#navbar-toggle')
toggleButton.addEventListener('click', function () {
  toggleButton.parentElement.nextElementSibling.classList.toggle('selected')
})

//to display items into dropdown element
const dropdownButtons = document.querySelectorAll('.nav-dropdown > button')
let selectedDropdown = null
const toggleSelectDropdown = function (event, currentDropdown) {
  event.preventDefault()

  if (currentDropdown === selectedDropdown) {
    currentDropdown.classList.remove('selected')
    selectedDropdown = null
  } else {
    if (selectedDropdown !== null) {
      selectedDropdown.classList.remove('selected')
    }
    currentDropdown.classList.add('selected')
    selectedDropdown = currentDropdown
  }
}
dropdownButtons.forEach(dropdownButton => {
  dropdownButton.addEventListener('click', (event) => toggleSelectDropdown(event, dropdownButton.nextElementSibling))
})
