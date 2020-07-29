import React from 'react'
import { render } from 'react-dom'
import SymfonyComponentWrapper from './ui/Form/SymfonyComponentWrapper'

/**
 *
 * @param {HTMLElement} parentForm
 */
const makeForm = function (parentForm) {
  parentForm.querySelectorAll('.js-form-component').forEach(function (field) {
    const props = { ...JSON.parse(field.getAttribute('props')) }
    render(<SymfonyComponentWrapper {...props} component={field.dataset.component}/>, field) //TODO: render a component with value and error being dynamic
  })
}

const parentForm = document.getElementById('register_form')
makeForm(parentForm)
