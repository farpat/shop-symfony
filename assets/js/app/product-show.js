import React from "react";
import {render} from "react-dom";
import {Provider} from "react-redux";
import {createStore} from "redux";
import reducers from "./Product/reducers";
import ProductComponent from "./Product/components/ProductComponent";

const productElement = document.querySelector('#product-component');
const {productReferences: productReferencesInString, currency} = productElement.dataset;
const productReferences = JSON.parse(productReferencesInString);
const currentReference = productReferences[0];

let activatedSliderIndexByReference = {};
productReferences.map(reference => activatedSliderIndexByReference[reference.id] = 0);

const data = {
    product: {
        productReferences,
        currentReference,
        currency,
        activatedSliderIndexByReference,
    }
};

render(<Provider store={createStore(reducers, data)}><ProductComponent/></Provider>, productElement);
