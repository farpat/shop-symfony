import React from 'react'
import { hot } from 'react-hot-loader/root'
import PropTypes from 'prop-types'
import {
  getHelpId,
  getInputClassName,
  getLabelClassName,
  getRules,
  isRequired,
  getValueFromEvent,
  useValueAndError, getError
} from './Form'

function CheckboxComponent ({ rulesInString, initialValue, initialError, attr, id, name, help, label }) {
  const rules = getRules(rulesInString)
  let required = isRequired(rules)
  const { value, error, setError, setValue } = useValueAndError(initialValue, initialError)

  return (
    <div className="form-group">
      <div className="custom-control custom-switch">

        <input type="checkbox" className={getInputClassName(error, 'custom-control-input')} id={id} name={name}
               required={required} aria-describedby={getHelpId(help, id)} checked={value}
               onChange={event => setValue(getValueFromEvent(event))}
               onBlur={() => setError(getError(rules, value))} {...attr}
        />

        {
          label !== '' &&
          <label htmlFor={id} className={getLabelClassName(error, 'custom-control-label')}>{label}</label>
        }

        {
          error !== '' &&
          <div className='invalid-feedback'>{error}</div>
        }


        {
          help &&
          <small id={getHelpId(help, id)} className='form-text text-muted w-100'>{help}</small>
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
  parentForm: PropTypes.instanceOf(HTMLFormElement),
  attr      : PropTypes.object,
  label     : PropTypes.string,
  rules     : PropTypes.string
}

export default hot(CheckboxComponent)
