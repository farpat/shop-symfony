import React from 'react'
import PropTypes from 'prop-types'
import { hot } from 'react-hot-loader/root'
import {
  getHelpId,
  getInputClassName,
  getLabelClassName,
  getRules,
  isRequired,
  updateError,
  updateValue,
  useValueAndError
} from './Form'

function PasswordComponent ({ label, name, attr, id, help, initialValue, initialError, rulesInString, withKey }) {
  const rules = getRules(rulesInString)
  let required = isRequired(rules)
  const { value, error, setError, setValue } = useValueAndError(initialValue, initialError)
  const inputElement = <input type="password" className={getInputClassName(error)} id={id} name={name}
                              required={required} aria-describedby={getHelpId(help, id)} value={value}
                              onChange={event => updateValue(setValue, event)}
                              onBlur={() => updateError(setError, rules, error, value)} {...attr}/>

  return (
    <div className="form-group">
      {
        label !== '' && <label htmlFor={id} className={getLabelClassName(required)}>{label}</label>
      }

      {
        withKey ?
          <div className="input-group">
            <div className='input-group-prepend'>
              <span className='input-group-text'><i className='fas fa-key'/></span>
            </div>
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
  withKey   : PropTypes.bool.isRequired,
  parentForm: PropTypes.instanceOf(HTMLFormElement),
  attr      : PropTypes.object,
  label     : PropTypes.string,
  rules     : PropTypes.string
}

export default hot(PasswordComponent)
