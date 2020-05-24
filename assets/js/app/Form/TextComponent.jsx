import React from 'react'
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
import { hot } from 'react-hot-loader/root'
import PropTypes from 'prop-types'

function TextComponent ({ label, name, attr, id, help, initialValue, initialError, rulesInString }) {
  const rules = getRules(rulesInString)
  let required = isRequired(rules)
  const { value, error, setError, setValue } = useValueAndError(initialValue, initialError)

  return (
    <div className="form-group">
      {
        label !== '' && <label htmlFor={id} className={getLabelClassName(required)}>{label}</label>
      }
      <input type="text" className={getInputClassName(error)} id={id} name={name}
             required={required} aria-describedby={getHelpId(help, id)} value={value}
             onChange={event => updateValue(setValue, event)}
             onBlur={() => updateError(setError, rules, error, value)} {...attr}
      />
      {
        error !== '' && <div className="invalid-feedback">{error}</div>
      }
      {
        help !== '' && <small id={getHelpId(help, id)} className='form-text text-muted w-100'>{help}</small>
      }
    </div>
  )
}

TextComponent.propTypes = {
  id        : PropTypes.string.isRequired,
  parentForm: PropTypes.instanceOf(HTMLFormElement).isRequired,
  attr      : PropTypes.object,
  label     : PropTypes.string,
  rules     : PropTypes.string
}

export default hot(TextComponent)
