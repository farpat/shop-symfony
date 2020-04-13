import React from "react";
import PropTypes from "prop-types";

class InputComponent extends React.Component {
    constructor(props) {
        super(props);

        this.required = false;
        this.rules = this.makeRules(this.props.rules);

        this.state = {
            value: this.props.type !== 'checkbox' ? this.props.value : this.props.value != 0,
            error: this.props.error || '',
        };

        this.changeValue = this.changeValue.bind(this);
        this.getError = this.getError.bind(this);
    }

    /**
     *
     * @param {String|undefined} implodedRules rule1|rule2:parameters2|etc.
     */
    makeRules(implodedRules) {
        if (implodedRules === undefined || implodedRules === '') {
            return [];
        }

        let rules = [];

        for (let ruleSplitted of implodedRules.split('²')) {

            let [ruleName, parametersInString] = ruleSplitted.split('ß');

            if (ruleName === 'NotBlank' || ruleName === 'IsTrue') {
                this.required = true;
            }

            const RuleClass = require(`../Security/Rules/${ruleName}Rule`).default;
            let parameters = {};
            parametersInString.split('@').map(function (parameterExploded) {
                let [key, value] = parameterExploded.split(':');
                parameters[key] = value;
            });

            rules.push(new RuleClass(parameters));
        }

        return rules;
    }

    getError(event) {
        const value = this.props.type === 'checkbox' ? event.currentTarget.checked : event.currentTarget.value;

        this.setState({
            error: this.getErrorValue(value)
        })
    }

    changeValue(event) {
        let newValue = this.props.type !== 'checkbox' ? event.currentTarget.value : event.currentTarget.checked;

        this.setState({value: newValue});
    }

    getErrorValue(value) {
        if (this.rules.length === 0) {
            return '';
        }

        for (let rule of this.rules) {
            let error;
            if (error = rule.check(value)) {
                return error;
            }
        }

        return '';
    }

    getInputValue() {
        if (this.props.type === 'checkbox') {
            return 1;
        }

        return this.state.value;
    }

    render() {
        return (
            <>
                <input
                    type={this.props.type} className={this.getInputClassName()} id={this.props.id}
                    name={this.props.name} required={this.required}
                    aria-describedby={this.props.help ? this.props.id + '_help' : ''}
                    value={this.getInputValue()} checked={this.state.value} onChange={this.changeValue}
                    onBlur={this.getError}
                    {...this.props.attr}
                />

                {
                    this.props.type === 'checkbox' &&
                    <label htmlFor={this.props.id} className={this.getLabelClassName()}>{this.props.label}</label>
                }

                {
                    this.state.error !== '' &&
                    <div className="invalid-feedback">{this.state.error}</div>
                }
                {
                    this.props.help &&
                    <small id={this.props.id + '_help'} className="form-text text-muted w-100">{this.props.help}</small>
                }
                {
                    this.props.type === 'checkbox' && !this.state.value &&
                    <input type='hidden' name={this.props.name} value="0"/>
                }
            </>
        );
    }

    getLabelClassName() {
        let className = 'custom-control-label';

        if (this.required) {
            className += ' required';
        }

        return className;
    }

    getInputClassName() {
        let className = this.props.type !== 'checkbox' ? 'form-control' : 'custom-control-input';

        if (this.state.error !== '') {
            className += ' is-invalid';
        }

        return className;
    }
}

InputComponent.propTypes = {
    id:         PropTypes.string.isRequired,
    type:       PropTypes.string.isRequired,
    name:       PropTypes.string.isRequired,
    parentForm: PropTypes.instanceOf(HTMLFormElement).isRequired,
    error:      PropTypes.string,
    withKey:    PropTypes.bool,
    attr:       PropTypes.object,
    help:       PropTypes.string,
    label:      PropTypes.string,
    rules:      PropTypes.string,
};

export default InputComponent;