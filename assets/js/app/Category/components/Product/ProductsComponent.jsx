import React from "react";
import PropTypes from "prop-types";
import {hot} from "react-hot-loader/root";
import ProductComponent from "./ProductComponent";
import ProductsNavigation from "./ProductsNavigation";

class ProductsComponent extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div className="products-component">
                <ProductsNavigation currentPage={this.props.currentPage}
                                    perPage={this.props.perPage}
                                    products={this.props.products}/>
                <div className="row">
                    {
                        this.props.products.map(product => {
                            return <ProductComponent key={product.id} product={product}/>
                        })
                    }
                </div>
            </div>
        );
    }
}

ProductsComponent.propTypes = {
    products:    PropTypes.arrayOf(PropTypes.shape({
        id:      PropTypes.number.isRequired,
        url:     PropTypes.string.isRequired,
        excerpt: PropTypes.string,
        label:   PropTypes.string.isRequired,
        image:   PropTypes.shape({
            url_thumbnail: PropTypes.string.isRequired
        })
    })),
    perPage:     PropTypes.number.isRequired,
    currentPage: PropTypes.number.isRequired
};

export default hot(ProductsComponent);
