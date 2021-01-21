import React from 'react'
import PropTypes from 'prop-types'
import { getHelpId, getInputClassName, getLabelClassName} from './Form'

function PasswordComponent ({ label, name, attr, id, help, value, isRequired, error, withKey, onUpdate = function () {} }) {
  const inputElement = <input type="password" className={getInputClassName(error)} id={id} name={name}
                              required={isRequired} aria-describedby={getHelpId(help, id)} defaultValue={value}
                              onChange={event => onUpdate(name, event.target.value)} {...attr}/>

  return (
    <div className="form-group">
      {
        label !== '' && <label htmlFor={id} className={getLabelClassName(isRequired)}>{label}</label>
      }

      {
        withKey ?
          <div className="input-group">
            <span className='input-group-text'><i className='fas fa-key'/></span>
            {inputElement}
          </div> :
          inputElement
      }

      {
        error !== '' && <div className="invalid-feedback">{error}</div>
      }
      {
        help !== '' && <small id={getHelpId(help, id)} className='form-text text-muted w-100'>{help}</small>
      }
    </div>
  )
}

PasswordComponent.propTypes = {
  id        : PropTypes.string.isRequired,
  name      : PropTypes.string.isRequired,
  isRequired: PropTypes.bool,
  withKey   : PropTypes.bool.isRequired,
  value     : PropTypes.string,
  error     : PropTypes.string,
  attr      : PropTypes.object,
  label     : PropTypes.string,
  onUpdate  : PropTypes.func
}

export default PasswordComponent
