import React from 'react'
import { hot } from 'react-hot-loader/root'
import PropTypes from 'prop-types'
import InputComponent from './InputComponent'

class TextComponent extends React.Component {
  constructor (props) {
    super(props)
  }

  render () {
    return (
      <div className='form-group'>
        {
          this.props.label !== '' &&
            <label htmlFor={this.props.id} className={this.getClassName()}>{this.props.label}</label>
        }

        <InputComponent {...this.props} type='text' />
      </div>
    )
  }

  isRequired () {
    return this.props.rules.includes('NotBlankß')
  }

  getClassName () {
    if (this.isRequired()) {
      return 'required'
    }

    return ''
  }
}

TextComponent.propTypes = {
  id: PropTypes.string.isRequired,
  parentForm: PropTypes.instanceOf(HTMLFormElement).isRequired,
  attr: PropTypes.object,
  label: PropTypes.string,
  rules: PropTypes.string
}

export default hot(TextComponent)
