import React from "react";
import {hot} from "react-hot-loader/root";
import PropTypes from "prop-types";
import NumberFieldContainer from "../../containers/ProductField/NumberFieldContainer";
import StringFieldContainer from "../../containers/ProductField/StringFieldContainer";

class ProductFieldsComponent extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div className="filter-component">
                {
                    this.props.productFields.map(productField => {
                        if (productField.type === 'number') {
                            return <NumberFieldContainer key={productField.key} productField={productField}/>
                        } else if (productField.type === 'string') {
                            return <StringFieldContainer key={productField.key} productField={productField}/>
                        }
                    })
                }
            </div>
        );
    }
}

ProductFieldsComponent.propTypes = {
    productFields: PropTypes.arrayOf(PropTypes.shape({
        key:   PropTypes.string.isRequired,
        type:  PropTypes.string.isRequired,
        label: PropTypes.string.isRequired
    }))
};

export default hot(ProductFieldsComponent);
