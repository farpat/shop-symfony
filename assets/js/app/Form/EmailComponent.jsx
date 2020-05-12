import React from 'react'
import PropTypes from 'prop-types'
import { hot } from 'react-hot-loader/root'
import InputComponent from './InputComponent'

class EmailComponent extends React.Component {
  constructor (props) {
    super(props)
  }

  render () {
    return (
      <div className='form-group'>
        {
          this.props.label !== '' &&
            <label htmlFor={this.props.id} className={this.getLabelClassName()}>{this.props.label}</label>
        }

        <div className='input-group'>
          <div className='input-group-prepend'>
            <span className='input-group-text'><i className='far fa-envelope-open' /></span>
          </div>

          <InputComponent {...this.props} type='email' />
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

EmailComponent.propTypes = {
  id: PropTypes.string.isRequired,
  parentForm: PropTypes.instanceOf(HTMLFormElement),
  attr: PropTypes.object,
  label: PropTypes.string,
  rules: PropTypes.string
}

export default hot(EmailComponent)
