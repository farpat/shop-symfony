import React from "react";
import PropTypes from "prop-types";
import {hot} from "react-hot-loader/root";
import StringFieldComponent from "../ProductField/StringFieldComponent";
import ProductComponent from "./ProductComponent";

class ProductsComponent extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div className="products-component">
                {
                    this.props.products.length > 0 &&
                    <nav aria-label="Product pagination" className="mt-2">
                        <ul className="pagination">
                            <li className="page-item"><a href="#" className="page-link">&larr; Previous</a></li>
                            <li className="page-item"><a href="#" className="page-link">1</a></li>
                            <li className="page-item"><a href="#" className="page-link">2</a></li>
                            <li className="page-item"><a href="#" className="page-link">Next &rarr;</a></li>
                        </ul>
                    </nav>
                }
                {
                    this.props.products.length > 0 &&
                    <div className="row">
                        {
                            this.props.products.map(product => {
                                return <ProductComponent key={product.id} product={product}/>
                            })
                        }
                    </div>
                }
                {
                    this.props > products.length > 0 &&
                    <p>Sorry, no products found!</p>
                }
            </div>
        );
    }
}

StringFieldComponent.propTypes = {
    products: PropTypes.arrayOf(PropTypes.shape({
        id:      PropTypes.number.isRequired,
        url:     PropTypes.string.isRequired,
        excerpt: PropTypes.string,
        label:   PropTypes.string.isRequired,
        image:   PropTypes.shape({
            url_thumbnail: PropTypes.string.isRequired
        })
    }))
};

export default hot(ProductsComponent);
