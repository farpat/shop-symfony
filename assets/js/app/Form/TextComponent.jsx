import React, { useEffect, useState } from 'react'
import { getHelpId, getInputClassName, getLabelClassName, getRulesFromBack, getError } from './Form'
import PropTypes from 'prop-types'

function TextComponent ({ label, name, attr, id, help, value, isRequired, error, onUpdate = function () {} }) {
  return (
    <div className="form-group">
      {
        label !== '' && <label htmlFor={id} className={getLabelClassName(isRequired)}>{label}</label>
      }
      <input type="text" className={getInputClassName(error)} id={id} name={name} defaultValue={value}
             onChange={event => onUpdate(name, event.target.value)}
             required={isRequired} aria-describedby={getHelpId(help, id)} {...attr}
      />
      {
        error && <div className="invalid-feedback">{error}</div>
      }
      {
        help !== '' && <small id={getHelpId(help, id)} className='form-text text-muted w-100'>{help}</small>
      }
    </div>
  )
}

TextComponent.propTypes = {
  id        : PropTypes.string.isRequired,
  name      : PropTypes.string.isRequired,
  isRequired: PropTypes.bool,
  value     : PropTypes.string,
  error     : PropTypes.string,
  attr      : PropTypes.object,
  label     : PropTypes.string,
  onUpdate  : PropTypes.func
}

export default TextComponent
