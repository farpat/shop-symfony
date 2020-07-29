import React from 'react'
import PropTypes from 'prop-types'

function Alert ({ type, message, onClose = function () {} }) {
  return (
    <div className={`alert alert-${type} alert-dismissible fade show`} role="alert">
      {message}
      <button type="button" className="close" data-dismiss="alert" aria-label="Close"
              onClick={onClose}>
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  )
}

Alert.propTypes = {
  type   : PropTypes.string.isRequired,
  message: PropTypes.string.isRequired,
  onClose: PropTypes.func,
}

export default Alert
