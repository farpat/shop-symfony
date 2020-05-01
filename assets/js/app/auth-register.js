import React from "react"
import {render} from "react-dom"


/**
 *
 * @param {HTMLElement} parentForm
 */
const makeForm = function (parentForm) {
    parentForm.querySelectorAll('.js-form-component').forEach(function (field) {
        const props = {...JSON.parse(field.getAttribute('props')), parentForm}
        const Component = require(`./Form/${field.dataset.component}`).default
        render(<Component {...props}/>, field)
    })
}

const parentForm = document.getElementById('register_form')
makeForm(parentForm)

