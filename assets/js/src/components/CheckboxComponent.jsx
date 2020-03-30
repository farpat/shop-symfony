import React from "react";
import PropTypes from 'prop-types';
import InputComponent from "./InputComponent";

class PasswordComponent extends React.Component {
    constructor(props) {
        super(props);

        console.log(props);
    }

    render() {
        return (
            <div className="form-group">
                <div className="custom-control custom-switch">
                    <InputComponent {...this.props} type="checkbox"/>
                </div>
            </div>
        );
    }

    isRequired() {
        return this.props.rules.includes('required');
    }

    getLabelClassName() {
        let className = 'custom-control-label';

        if (this.isRequired()) {
            className += ' required';
        }

        return className;
    }
}

PasswordComponent.propTypes = {
    id:         PropTypes.string.isRequired,
    parentForm: PropTypes.instanceOf(HTMLFormElement),
    attr:       PropTypes.object,
    label:      PropTypes.string,
    rules:      PropTypes.string,
};

export default PasswordComponent;