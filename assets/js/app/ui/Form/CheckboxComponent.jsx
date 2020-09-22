import React, { forwardRef } from 'react'
import PropTypes from 'prop-types'
import { getHelpId, getInputClassName, getLabelClassName } from './Form'

const CheckboxComponent = forwardRef(function ({ label, name, attr, wrapperClassName, id, help, value, isRequired, error, onUpdate = function () {} }, ref) {
  return (
    <div className={`form-group ${wrapperClassName || ''}`}>
      <div className="form-check form-switch">

        <input type="checkbox" className={getInputClassName(error, 'form-check-input')} id={id} name={name}
               required={isRequired} aria-describedby={getHelpId(help, id)} checked={value} value='1'
               onChange={event => onUpdate(name, event.target.checked)} {...attr}
        />

        {
          label !== '' && <label htmlFor={id} className={getLabelClassName(error, 'form-check-label')}>{label}</label>
        }

        {
          error !== '' && <div className='invalid-feedback'>{error}</div>
        }


        {
          help && <small id={getHelpId(help, id)} className='form-text text-muted w-100'>{help}</small>
        }
        {
          value === false && <input type='hidden' name={name} value='0'/>
        }
      </div>
    </div>
  )
})

CheckboxComponent.propTypes = {
  label           : PropTypes.string,
  name            : PropTypes.string.isRequired,
  attr            : PropTypes.object,
  wrapperClassName: PropTypes.string,
  id              : PropTypes.string.isRequired,
  help            : PropTypes.string,
  value           : PropTypes.bool,
  isRequired      : PropTypes.bool,
  error           : PropTypes.string,
  onUpdate        : PropTypes.func
}

export default CheckboxComponent
