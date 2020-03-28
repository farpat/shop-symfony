import React, {Suspense} from "react";
import ReactDOM from "react-dom";

const registerFormElement = document.querySelector('form[name="register_form"]');

document.querySelectorAll('.js-form-component').forEach(function (field) {
    const props = JSON.parse(field.getAttribute('props'));
    props['parentForm'] = registerFormElement;
    const Component = React.lazy(() => import(`../src/components/${field.dataset.component}`));
    ReactDOM.render(<Suspense fallback={<div>Chargement...</div>}>
            <Component {...props}/>
        </Suspense>,
        field
    );
});