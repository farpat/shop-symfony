import React, { forwardRef, useState } from 'react'
import { getHelpId, getInputClassName, getLabelClassName } from './Form'
import PropTypes from 'prop-types'
import Str from '../../../src/Str'

const FileComponent = forwardRef(function ({
  label,
  name,
  inputClassName,
  wrapperClassName,
  attr,
  id,
  help,
  initialText,
  currentText,
  buttonText,
  isRequired,
  error,
  onDelete = function () { },
  onUpdate = function () { }
}, ref) {
  const isMultiple = name.substr(-2) === '[]'
  const [text, setText] = useState(currentText || initialText)

  return (
    <div className={'form-file mb-3' + (wrapperClassName || '')}>
      <input type="file" className={(inputClassName || '') + ' ' + getInputClassName(error)} id={id} name={name}
             ref={ref}
             onChange={event => {
               const files = event.target.files
               let value

               if (isMultiple) {
                 value = files
                 setText('Multiple files')
               } else {
                 value = files[0]
                 setText(`${value.name} (${Str.bytesToSize(value.size)})`)
               }

               onUpdate(name, value)
             }}
             required={isRequired} aria-describedby={getHelpId(help, id)} {...attr}
      />
      <label htmlFor={id} className={getLabelClassName(isRequired, 'form-file-label')} style={{ cursor: 'pointer' }}>
        <span className="form-file-text text-black-50">{text}</span>
        <FileButton isFilled={text !== initialText} buttonText={buttonText} onDeletePicture={() => {
          setText(initialText)
          onDelete(name)
        }}/>
      </label>
      {
        error && <div className="invalid-feedback">{error}</div>
      }
      {
        help !== '' && <small id={getHelpId(help, id)} className='form-text text-muted w-100'>{help}</small>
      }
    </div>
  )
})

function FileButton ({ isFilled, onDeletePicture, buttonText }) {
  const handleClick = function () {
    if (isFilled) {
      onDeletePicture()
    }
  }

  if (isFilled) {
    return <button type="button" className="form-file-button text-danger" onClick={handleClick} title="Delete image"
                   dangerouslySetInnerHTML={{ __html: '&times;' }}/>
  }

  return <button type="button" className="form-file-button" onClick={handleClick}>
    {buttonText}
  </button>
}

FileComponent.propTypes = {
  id         : PropTypes.string.isRequired,
  name       : PropTypes.string.isRequired,
  className  : PropTypes.string,
  initialText: PropTypes.string,
  isRequired : PropTypes.bool,
  value      : PropTypes.string,
  error      : PropTypes.string,
  attr       : PropTypes.object,
  label      : PropTypes.string,
  onUpdate   : PropTypes.func,
  onDelete   : PropTypes.func,
}

export default FileComponent
