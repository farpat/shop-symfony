import React from 'react'
import PropTypes from 'prop-types'
import { getHelpId, getInputClassName, getLabelClassName } from './Form'
import TextComponent from './TextComponent'

function CheckboxComponent ({ label, name, attr, id, help, value, isRequired, error, onUpdate = function () {} }) {
  return (
    <div className="form-group">
      <div className="form-check form-switch">

        <input type="checkbox" className={getInputClassName(error, 'form-check-input')} id={id} name={name}
               required={isRequired} aria-describedby={getHelpId(help, id)} checked={value}
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
}

CheckboxComponent.propTypes = {
  id        : PropTypes.string.isRequired,
  name      : PropTypes.string.isRequired,
  isRequired: PropTypes.bool,
  value     : PropTypes.bool,
  error     : PropTypes.string,
  attr      : PropTypes.object,
  label     : PropTypes.string,
  onUpdate  : PropTypes.func
}

export default CheckboxComponent
