import React from "react";
import {render} from "react-dom";
import {Provider} from "react-redux";
import {createStore} from "redux";
import reducers from "./Category/reducers/index";
import ProductFieldsContainer from "./Category/containers/ProductField/ProductFieldsContainer";

const productElement = document.querySelector('#products-component');
if (productElement) {
    const {products: productsInString, currentPage, perPage} = productElement.dataset;
    const productFieldsElement = document.querySelector('#product-fields-component');

    const data = {
        products:      {
            allProducts:       JSON.parse(productsInString),
            productsToDisplay: [],
            currentPage:       Number.parseInt(currentPage),
            perPage:           Number.parseInt(perPage),
        },
        productFields: {
            allProductFields: productFieldsElement ? JSON.parse(productFieldsElement.dataset.productFields) : null,
            filters:          {"color-1": "red"},
        },
    };

    const store = createStore(reducers, data);

    // render(<Provider store={store}><ProductsComponent/></Provider>, productElement);

    if (data.productFields.allProductFields.length > 0) {
        render(<Provider store={store}><ProductFieldsContainer/></Provider>, productFieldsElement);
    }
}









