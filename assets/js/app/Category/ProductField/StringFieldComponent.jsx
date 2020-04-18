import React from "react";
import PropTypes from "prop-types";

class StringFieldComponent extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div className="form-group">
                <p className="mb-1">{this.props.productField.label}</p>
                <input type="text" placeholder={this.props.productField.label} className="form-control"/>
            </div>
        );
    }
}

StringFieldComponent.propTypes = {
    productField: PropTypes.shape({
        id:    PropTypes.number.isRequired,
        label: PropTypes.string.isRequired
    })
};

export default StringFieldComponent;