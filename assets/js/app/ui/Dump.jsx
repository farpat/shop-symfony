import React from 'react'
import PropTypes from 'prop-types'
import Str from '../../src/Str'

function Dump ({ object }) {
  return (
    <div dangerouslySetInnerHTML={{ __html: Str.dump(object) }}></div>
  )
}

Dump.propTypes = {
  object: PropTypes.any.isRequired,
}

export default Dump
