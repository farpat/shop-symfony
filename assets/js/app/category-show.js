import React from "react";
import {render} from "react-dom";
import {Provider} from "react-redux";
import {createStore} from "redux";
import reducers from "./Category/reducers";
import ProductFieldsComponent from "./Category/components/ProductField/ProductFieldsComponent";
import ProductsComponent from "./Category/components/Product/ProductsComponent";

const productsElement = document.querySelector('#products-component');
if (productsElement) {
    const {products: productsInString, currentPage, perPage, currency} = productsElement.dataset;
    const productFieldsElement = document.querySelector('#product-fields-component');

    const data = {
        products: {
            allProducts:      JSON.parse(productsInString),
            allProductFields: productFieldsElement ? JSON.parse(productFieldsElement.dataset.productFields) : null,
            perPage:          Number.parseInt(perPage),
            currency,
            currentProducts:  [],
            currentPage:      Number.parseInt(currentPage),
            currentFilters:   {},
        }
    };

    const store = createStore(reducers, data);

    render(<Provider store={store}><ProductsComponent/></Provider>, productsElement);

    if (data.products.allProductFields.length > 0) {
        render(<Provider store={store}><ProductFieldsComponent/></Provider>, productFieldsElement);
    }
}









