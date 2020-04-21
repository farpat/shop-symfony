import React from "react";
import PropTypes from "prop-types";
import {hot} from "react-hot-loader/root";
import {connect} from "react-redux";
import ProductComponent from "./ProductComponent";
import ProductsNavigation from "./ProductsNavigation";

class ProductsComponent extends React.Component {
    constructor(props) {
        super(props);
    }

    getProductsToDisplay() {
        const start = (this.props.currentPage - 1) * this.props.perPage;
        return this.props.products.slice(start, start + this.props.perPage);
    }


    render() {
        const productsToDisplay = this.getProductsToDisplay();

        return (
            <div className="products-component">
                <ProductsNavigation/>
                <div className="row">
                    {
                        productsToDisplay.length > 0 &&
                        productsToDisplay.map(product => {
                            return <ProductComponent key={product.id} product={product}/>
                        })
                    }
                    {
                        productsToDisplay.length === 0 &&
                        <p>Sorry! There are no products</p>
                    }
                </div>
            </div>
        );
    }
}

ProductsComponent.propTypes = {
    products:    PropTypes.arrayOf(PropTypes.shape({
        id:                             PropTypes.number.isRequired,
        url:                            PropTypes.string.isRequired,
        excerpt:                        PropTypes.string,
        label:                          PropTypes.string.isRequired,
        min_unit_price_including_taxes: PropTypes.number.isRequired,
        image:                          PropTypes.shape({
            url_thumbnail: PropTypes.string.isRequired,
            alt_thumbnail: PropTypes.string,
        })
    })),
    currentPage: PropTypes.number.isRequired,
    perPage:     PropTypes.number.isRequired,
};

const mapStateToProps = (state) => {
    return {
        products:    state.products.currentProducts,
        currentPage: state.products.currentPage,
        perPage:     state.products.perPage,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {}
};

export default connect(mapStateToProps, mapDispatchToProps)(hot(ProductsComponent));
