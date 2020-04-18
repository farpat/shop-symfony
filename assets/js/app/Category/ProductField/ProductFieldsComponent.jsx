import React from "react";
import PropTypes from "prop-types";
import NumberFieldComponent from "./NumberFieldComponent";
import StringFieldComponent from "./StringFieldComponent";

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
                            return <NumberFieldComponent key={productField.id} productField={productField}/>
                        } else if (productField.type === 'string') {
                            return <StringFieldComponent key={productField.id} productField={productField}/>
                        }
                    })
                }
            </div>
        );
    }
}

ProductFieldsComponent.propTypes = {
    productFields: PropTypes.arrayOf(PropTypes.shape({
        id:    PropTypes.number.isRequired,
        type:  PropTypes.string.isRequired,
        label: PropTypes.string.isRequired
    })),
};

export default ProductFieldsComponent;