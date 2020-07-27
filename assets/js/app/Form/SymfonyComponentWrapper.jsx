import React, { useState, useMemo } from 'react'
import { hot } from 'react-hot-loader/root'
import PropTypes from 'prop-types'
import { getError, getRulesFromBack } from './Form'

function SymfonyComponentWrapper (props) {
  const Component = require(`./${props.component}`).default
  const [data, setData] = useState({
    error: props.initialError,
    value: props.initialValue
  })

  const rules = useMemo(() => {
    return { [props.name]: getRulesFromBack(props.rulesInString) }
  }, [props.rulesInString])

  const isRequired = useMemo(() => {
    const rule = rules[props.name]
    if (rule === undefined) {
      return false
    }
    return rule.find(r => r.name === 'not-blank') !== undefined
  }, [props.rulesInString])

  const onUpdate = function (key, value) {
    setData({ error: getError(rules, key, value), value })
  }

  return (
    <>
      <Component {...props} isRequired={isRequired} error={data.error} value={data.value} onUpdate={onUpdate}/>
    </>
  )
}

SymfonyComponentWrapper.propTypes = {
  component    : PropTypes.string.isRequired,
  id           : PropTypes.string.isRequired,
  name         : PropTypes.string.isRequired,
  label        : PropTypes.string.isRequired,
  initialValue : PropTypes.string.isRequired,
  initialError : PropTypes.string.isRequired,
  rulesInString: PropTypes.string.isRequired,
  attr         : PropTypes.object.isRequired,
  help         : PropTypes.string,
  withKey      : PropTypes.bool,
}

export default hot(SymfonyComponentWrapper)
