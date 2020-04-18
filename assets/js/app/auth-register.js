import React from "react";
import ReactDOM from "react-dom";
import {AppContainer} from "react-hot-loader";

/**
 *
 * @param {React.Component} Component
 * @param {HTMLElement} field
 * @param {Object} props
 */
const render = function (Component, field, props) {
    ReactDOM.render(<AppContainer><Component {...props}/></AppContainer>, field);
}

/**
 *
 * @param {HTMLElement} parentForm
 */
const makeForm = function (parentForm) {
    parentForm.querySelectorAll('.js-form-component').forEach(function (field) {
        const props = {
            ...JSON.parse(field.getAttribute('props')),
            parentForm
        };

        const Component = require(`./Form/${field.dataset.component}`).default;
        render(Component, field, props);
    });
}

const parentForm = document.getElementById('register_form');
makeForm(parentForm);

