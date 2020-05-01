import React from "react"
import PropTypes from "prop-types"
import {connect} from "react-redux"

class StringFieldComponent extends React.Component {
    constructor(props) {
        super(props)
        this.changeValue = this.changeValue.bind(this)
    }

    changeValue(event) {
        this.props.updateFilter(this.props.productField.key, event.target.value)
    }

    getValue(key) {
        return this.props.filters[key] || ''
    }

    render() {
        return (
            <div className="form-group">
                <p className="mb-1">{this.props.productField.label}</p>
                <input name={this.props.productField.key} type="text"
                       value={this.getValue(this.props.productField.key)}
                       onChange={this.changeValue}
                       placeholder={this.props.productField.label}
                       className="form-control"/>
            </div>
        )

    }
}

StringFieldComponent.propTypes = {
    productField: PropTypes.shape({
        key:   PropTypes.string.isRequired,
        label: PropTypes.string.isRequired
    }),


    updateFilter: PropTypes.func.isRequired
}

const mapStateToProps = (state) => {
    return {
        filters: state.currentFilters
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        updateFilter: (key, value) => {
            dispatch({type: 'UPDATE_FILTER', key, value})
        }
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(StringFieldComponent)
