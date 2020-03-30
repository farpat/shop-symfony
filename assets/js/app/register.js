import React, {Suspense} from "react";
import {render} from "react-dom";

const parentForm = document.getElementById('register_form');
makeForm(parentForm);


function makeForm(formElement) {
    parentForm.querySelectorAll('.js-form-component').forEach(function (field) {
        const props = {
            ...JSON.parse(field.getAttribute('props')),
            parentForm
        };

        const Component = React.lazy(() => import(`../src/components/${field.dataset.component}`));

        render(<Suspense fallback={<div>Chargement...</div>}><Component {...props}/></Suspense>, field);
    });
}