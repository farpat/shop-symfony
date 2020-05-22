import React from 'react'
import PropTypes from 'prop-types'
import { hot } from 'react-hot-loader/root'
import InputComponent from './InputComponent'

class PasswordComponent extends React.Component {
  render () {
    return (
      <div className='form-group'>
        {
          this.props.label !== '' &&
          <label htmlFor={this.props.id} className={this.getLabelClassName()}>{this.props.label}</label>
        }

        <div className='input-group'>
          {
            this.props.withKey &&
            <div className='input-group-prepend'>
              <span className='input-group-text'><i className='fas fa-key'/></span>
            </div>
          }

          <InputComponent {...this.props} type='password'/>
        </div>
      </div>
    )
  }

  isRequired () {
    return this.props.rules.includes('NotBlankß')
  }

  getLabelClassName () {
    if (this.isRequired()) {
      return 'required'
    }

    return ''
  }
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
