import React, { forwardRef, useEffect } from 'react'
import { getHelpId, getLabelClassName } from './Form'
import PropTypes from 'prop-types'
import Choices from 'choices.js'
import 'choices.js/public/assets/styles/choices.css'

const ChoiceComponent = forwardRef(function ({ label, name, selectClassName, wrapperClassName, attr, id, help, value, choices, isRequired, error, onUpdate = function () { } }, ref) {
  useEffect(() => {
    new Choices(document.getElementById(id))
  }, [])

  return <div className={`form-group ${wrapperClassName || ''}`}>
    {
      label && <label htmlFor={id} className={getLabelClassName(isRequired)}>{label}</label>
    }
    <select aria-describedby={getHelpId(help, id)} id={id} name={name} ref={ref} {...attr}
            defaultValue={value} required={isRequired}
            onChange={event => onUpdate(name, event.target.value)}>
      {
        choices.map(choice => <option value={choice.value} key={choice.value}>{choice.label}</option>)
      }
    </select>
    {
      error && <div className="invalid-feedback">{error}</div>
    }

    {
      help && <small id={getHelpId(help, id)} className='form-text text-muted w-100'>{help}</small>
    }
  </div>
})

ChoiceComponent.propTypes = {
  label           : PropTypes.string,
  name            : PropTypes.string.isRequired,
  selectClassName : PropTypes.string,
  wrapperClassName: PropTypes.string,
  attr            : PropTypes.object,
  id              : PropTypes.string.isRequired,
  help            : PropTypes.string,
  value           : PropTypes.any,
  choices         : PropTypes.array.isRequired,
  isRequired      : PropTypes.bool,
  error           : PropTypes.string,
  onUpdate        : PropTypes.func
}

export default ChoiceComponent
