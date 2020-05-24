import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { changeValue, getValue } from './ProductField'

function NumberFieldComponent ({ productField, updateFilter, filters }) {
  const getFilterKey = function (suffix) {
    return `${productField.key}-${suffix}`
  }

  const filterKeyMin = getFilterKey('min')
  const filterKeyMax = getFilterKey('max')

  return (
    <div className='form-group'>
      <p className='mb-1'>{productField.label}</p>
      <div className='row no-gutters'>
        <div className='col'>
          <input
            name={getFilterKey('min')} value={getValue(filterKeyMin)}
            onChange={event => changeValue(filterKeyMin, updateFilter, event)}
            className='form-control'
            placeholder='min' type='number'
          />
        </div>
        <div className='col'>
          <input
            name={getFilterKey('max')} value={getValue(filterKeyMax)}
            onChange={event => changeValue(filterKeyMax, updateFilter, event)}
            className='form-control'
            placeholder='max' type='number'
          />
        </div>
      </div>
    </div>
  )
}

NumberFieldComponent.propTypes = {
  productField: PropTypes.shape({
    key  : PropTypes.string.isRequired,
    label: PropTypes.string.isRequired
  }),

  updateFilter: PropTypes.func.isRequired,
  filters     : PropTypes.object.isRequired,
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
