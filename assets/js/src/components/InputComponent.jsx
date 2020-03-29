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

        return implodedRules.split('|').map(function (ruleInString) {
            const RuleClass = require(`../Security/Rules/${ruleInString}Rule`).default;

            return new RuleClass();
        });
    }

    getError(event) {
        const value = event.currentTarget.value;

        this.setState({
            error: this.getErrorValue(value)
        })
    }

    changeValue(event) {
        const newValue = event.currentTarget.value;

        this.setState({
            value: newValue,
        });
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

    render() {
        return (
            <>
                <input
                    type={this.props.type} className={this.getClassName()} id={this.props.id}
                    value={this.state.value} onChange={this.changeValue} onBlur={this.getError}
                    {...this.props.attr}
                />
                {
                    this.state.error !== '' &&
                    <div className="invalid-feedback">{this.state.error}</div>
                }
            </>
        );
    }

    getClassName() {
        let className = 'form-control';

        if (this.state.error !== '') {
            className += ' is-invalid';
        }

        return className;
    }
}

InputComponent.propTypes = {
    parentForm: PropTypes.instanceOf(HTMLFormElement).isRequired,
    attr:       PropTypes.object,
    id:         PropTypes.string.isRequired,
    label:      PropTypes.string,
    rules:      PropTypes.string,
};

export default InputComponent;