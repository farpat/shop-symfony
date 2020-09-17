import React, { forwardRef } from 'react'
import { getHelpId, getInputClassName, getLabelClassName } from './Form'
import PropTypes from 'prop-types'

const TextComponent = forwardRef(function ({ label, name, inputClassName, wrapperClassName, attr, id, help, value, isRequired, error, onUpdate = function () { } }, ref) {
  return (
    <div className={'form-group ' + (wrapperClassName || '')}>
      {
        label && <label htmlFor={id} className={getLabelClassName(isRequired)}>{label}</label>
      }
      <input type="text" className={(inputClassName || '') + ' ' + getInputClassName(error)} id={id} name={name}
             ref={ref}
             defaultValue={value}
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
})

TextComponent.propTypes = {
  id        : PropTypes.string.isRequired,
  name      : PropTypes.string.isRequired,
  className : PropTypes.string,
  isRequired: PropTypes.bool,
  value     : PropTypes.string,
  error     : PropTypes.string,
  attr      : PropTypes.object,
  label     : PropTypes.string,
  onUpdate  : PropTypes.func
}

export default TextComponent
