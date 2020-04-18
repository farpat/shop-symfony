import React from "react";
import PropTypes from "prop-types";

class NumberFieldComponent extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div className="form-group">
                <p className="mb-1">{this.props.productField.label}</p>
                <div className="row no-gutters">
                    <div className="col">
                        <input className="form-control" placeholder="min" type="number"/>
                    </div>
                    <div className="col">
                        <input className="form-control" placeholder="max" type="number"/>
                    </div>
                </div>
            </div>
        );
    }
}

NumberFieldComponent.propTypes = {
    productField: PropTypes.shape({
        id:    PropTypes.number.isRequired,
        label: PropTypes.string.isRequired
    })
};

export default NumberFieldComponent;