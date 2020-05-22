import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'

class NumberFieldComponent extends React.Component {
  changeValue (suffix, event) {
    this.props.updateFilter(this.getFilterKey(suffix), event.target.value)
  }

  getValue (key) {
    return this.props.filters[key] || ''
  }

  getFilterKey (suffix) {
    return `${this.props.productField.key}-${suffix}`
  }

  render () {
    return (
      <div className='form-group'>
        <p className='mb-1'>{this.props.productField.label}</p>
        <div className='row no-gutters'>
          <div className='col'>
            <input
              name={this.getFilterKey('min')} value={this.getValue(this.getFilterKey('min'))}
              onChange={this.changeValue.bind(this, 'min')}
              className='form-control'
              placeholder='min' type='number'
            />
          </div>
          <div className='col'>
            <input
              name={this.getFilterKey('max')} value={this.getValue(this.getFilterKey('max'))}
              onChange={this.changeValue.bind(this, 'max')}
              className='form-control'
              placeholder='max' type='number'
            />
          </div>
        </div>
      </div>
    )
  }
}

NumberFieldComponent.propTypes = {
  productField: PropTypes.shape({
    key: PropTypes.string.isRequired,
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
    updateFilter: (key, value) => dispatch({ type: 'UPDATE_FILTER', key, value })
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(NumberFieldComponent)
