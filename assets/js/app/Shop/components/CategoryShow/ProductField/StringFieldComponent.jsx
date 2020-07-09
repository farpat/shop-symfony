import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { changeValue, getValue } from './ProductField'

function StringFieldComponent ({ productField, updateFilter, filters }) {
  return (
    <div className='form-group'>
      <label for={productField.key}>{productField.label}</label>
      <input
        id={productField.key}
        name={productField.key} type='text'
        value={getValue(filters, productField.key)}
        onChange={event => changeValue(productField.key, updateFilter, event)}
        placeholder={productField.label}
        className='form-control'
      />
    </div>
  )
}

StringFieldComponent.propTypes = {
  productField: PropTypes.shape({
    key  : PropTypes.string.isRequired,
    label: PropTypes.string.isRequired
  }),

  filters     : PropTypes.object.isRequired,
  updateFilter: PropTypes.func.isRequired,
}

const mapStateToProps = (state) => {
  return {
    filters: state.currentFilters
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    updateFilter: (key, value) => {
      dispatch({ type: 'UPDATE_FILTER', key, value })
    }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(StringFieldComponent)
