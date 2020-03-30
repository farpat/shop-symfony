import React from "react";
import PropTypes from "prop-types";

class InputComponent extends React.Component {
    constructor(props) {
        super(props);

        this.rules = this.makeRules(this.props.rules);

        this.state = {
            value: this.props.value,
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

            let [ruleName, parameters] = ruleSplitted.split('ß');

            const RuleClass = require(`../Security/Rules/${ruleName}Rule`).default;
            let rule;
            if (parameters) {
                let parameter = {};
                parameters.split('@').map(function (parameterExploded) {
                    let [key, value] = parameterExploded.split(':');
                    parameter[key] = value;
                });

                rule = new RuleClass(parameter);
            } else {
                rule = new RuleClass();
            }

            rules.push(rule);
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
            if (!rule.check(value)) {
                return 'error with ' + rule.name;
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
                    aria-describedby={this.props.id + '_help'}
                    value={this.getInputValue()} checked={this.state.value} onChange={this.changeValue}
                    onBlur={this.getError}
                    {...this.props.attr}
                />
                {
                    this.props.type === 'checkbox' &&
                    <label htmlFor={this.props.id} className="custom-control-label">{this.props.label}</label>
                }
                {
                    this.state.error !== '' &&
                    <div className="invalid-feedback">{this.state.error}</div>
                }
                {
                    this.props.help &&
                    <small id={this.props.id + '_help'} className="form-text text-muted w-100">{this.props.help}</small>
                }
            </>
        );
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
    parentForm: PropTypes.instanceOf(HTMLFormElement).isRequired,
    error:      PropTypes.string,
    withKey:    PropTypes.bool,
    attr:       PropTypes.object,
    help:       PropTypes.string,
    label:      PropTypes.string,
    rules:      PropTypes.string,
};

export default InputComponent;