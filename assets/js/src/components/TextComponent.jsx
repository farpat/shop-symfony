import React from "react";
import PropTypes from 'prop-types';

class TextComponent extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div className="form-group">
                <label htmlFor={this.props.id} className={this.getClassName()}>{this.props.label}</label>

                {
                    this.props.type === 'email' &&
                    <div className="input-group">
                        <div className="input-group-prepend">
                            <span className="input-group-text">@</span>
                        </div>
                        <input type={this.props.type} className="form-control" id={this.props.id}/>
                    </div>
                }


                {
                    this.props.type === 'text' &&
                    <input type={this.props.type} className="form-control" id={this.props.id}/>
                }
            </div>
        );
    }

    isRequired() {
        return this.props.rules.includes('required');
    }

    getClassName() {
        if (this.isRequired()) {
            return 'required';
        }

        return '';
    }
}

TextComponent.propTypes = {
    parentForm: PropTypes.instanceOf(HTMLFormElement),
    id:         PropTypes.string,
    type:       PropTypes.string, //text, email
    label:      PropTypes.string,
    rules:      PropTypes.string,
};

export default TextComponent;