import { useState } from 'react'

export function getRules (rulesInString) {
  if (rulesInString === undefined || rulesInString === '') {
    return []
  }

  const rules = []

  for (const ruleSplitted of rulesInString.split('²')) {
    const [ruleName, parametersInString] = ruleSplitted.split('ß')

    const RuleClass = require(`../../src/Security/Rules/${ruleName}Rule`).default
    const parameters = {}
    parametersInString.split('@').map(function (parameterExploded) {
      const [key, value] = parameterExploded.split(':')
      parameters[key] = value
    })

    rules.push(new RuleClass(parameters))
  }
  return rules
}

export function isRequired (rules) {
  return rules.find(rule => rule.name === 'not-blank' || rule.name === 'is-true') !== undefined
}

export function useValueAndError (initialValue, initialError) {
  const [value, setValue] = useState(initialValue)
  const [error, setError] = useState(initialError)

  return {
    value, setValue,
    error, setError
  }
}

export function getHelpId (help, id) {
  return help !== '' ? `${id}_help` : false
}

export function getLabelClassName (required, initialClassName = '') {
  let className = initialClassName

  if (required) {
    className += ' required'
  }

  return className
}

export function getInputClassName (error, initialClassName = 'form-control') {
  let className = initialClassName

  if (error !== '') {
    className += ' is-invalid'
  }

  return className
}

export function getError (rules, value) {
  if (rules.length === 0) {
    return ''
  }

  for (const rule of rules) {
    let error = rule.check(value)
    if (error !== '') {
      return error
    }
  }

  return ''
}

export function getValueFromEvent (event) {
  if (event.target.tagName === 'INPUT' && event.target.type === 'checkbox') {
    return event.target.checked
  } else {
    return event.target.value
  }
}