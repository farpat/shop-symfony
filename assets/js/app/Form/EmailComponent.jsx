import React, { useEffect } from 'react'
import PropTypes from 'prop-types'
import { hot } from 'react-hot-loader/root'
import {
  getHelpId,
  getInputClassName,
  getLabelClassName,
  getError
} from './Form'

function EmailComponent ({ label, name, attr, id, help, value, error, isRequired, onUpdate = function () {} }) {
  return (
    <div className="form-group">
      {
        label !== '' && <label htmlFor={id} className={getLabelClassName(isRequired)}>{label}</label>
      }

      <div className="input-group">
        <span className='input-group-text'><i className='far fa-envelope-open'/></span>

        <input type="email" className={getInputClassName(error)} id={id} name={name}
               onChange={event => onUpdate(name, event.target.value)}
               required={isRequired} aria-describedby={getHelpId(help, id)} defaultValue={value} {...attr}/>
      </div>

      {
        error && <div className="invalid-feedback">{error}</div>
      }

      {
        help !== '' && <small id={getHelpId(help, id)} className='form-text text-muted w-100'>{help}</small>
      }
    </div>
  )
}

EmailComponent.propTypes = {
  id        : PropTypes.string.isRequired,
  name      : PropTypes.string.isRequired,
  isRequired: PropTypes.bool,
  attr      : PropTypes.object,
  label     : PropTypes.string,
  onUpdate  : PropTypes.func
}

export default hot(EmailComponent)
