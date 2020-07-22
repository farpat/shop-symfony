import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import productAndCartStore from './Shop/services/ProductAndCartStore'
import HeadCartComponent from './Shop/components/HeadCart/CartComponent'

//Header cart component (in navbar)
const cartNavElement = document.querySelector('#cart-nav')
render(
  <Provider store={productAndCartStore}>
    <HeadCartComponent/>
  </Provider>,
  cartNavElement
)

//in little screen, to display menu
const toggleButton = document.querySelector('#navbar-toggle')
toggleButton.addEventListener('click', function () {
  toggleButton.parentElement.nextElementSibling.classList.toggle('selected')
})

//to display items into dropdown element
const dropdowns = document.querySelectorAll('.nav-dropdown > button')
let selectedDropdown = null
dropdowns.forEach(dropdown => {
  dropdown.addEventListener('click', function (event) {
    event.preventDefault()
    const dropdownMenuElement = dropdown.nextElementSibling
    const isSelected = dropdownMenuElement.classList.contains('selected')

    if (selectedDropdown) {
      selectedDropdown.nextElementSibling.classList.remove('selected')
      selectedDropdown = null
    }

    if (isSelected) {
      dropdownMenuElement.classList.remove('selected')
    } else {
      selectedDropdown = dropdown
      dropdownMenuElement.classList.add('selected')
    }
  })
})