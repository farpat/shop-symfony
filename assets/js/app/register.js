import React, {Suspense} from "react";
import ReactDOM from "react-dom";
import TextComponent from "../src/components/TextComponent";
import EmailComponent from "../src/components/EmailComponent";
import PasswordComponent from "../src/components/PasswordComponent";

const parentForm = document.querySelector('form[name="register_form"]');

document.querySelectorAll('.js-form-component').forEach(function (field) {
    const props = {
        ...JSON.parse(field.getAttribute('props')),
        parentForm
    };

    let Component;
    switch (field.dataset.component) {
        case 'TextComponent':
            Component = TextComponent;
            break;
        case 'EmailComponent':
            Component = EmailComponent;
            break;
        case 'PasswordComponent':
            Component = PasswordComponent;
            break;
        default:
            throw `src/components/${field.dataset.component} doesn't exist!`;
    }
    ReactDOM.render(<Suspense fallback={<div>Chargement...</div>}><Component {...props}/></Suspense>, field);
});