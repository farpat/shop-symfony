import React from "react";
import {render} from "react-dom";
import ProductFieldsComponent from "./Category/ProductField/ProductFieldsComponent";
import ProductsComponent from "./Category/Product/ProductsComponent";

const productFieldsElement = document.querySelector('#product-fields-component');
if (productFieldsElement) {
    const productFields = JSON.parse(productFieldsElement.dataset.productFields);
    render(<ProductFieldsComponent productFields={productFields}/>, productFieldsElement);
}

const productElement = document.querySelector('#products-component');
const products = JSON.parse(productElement.dataset.products);
render(<ProductsComponent products={products}/>, productElement);

