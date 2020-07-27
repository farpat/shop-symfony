import { useState } from 'react'

/**
 *
 * @param {String} rulesInString
 * @returns {Array}
 */
export function getRulesFromBack (rulesInString) {
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

/**
 *
 * @param {String} help
 * @param {String} id
 * @returns {false|String}
 */
export function getHelpId (help, id) {
  return help !== '' ? `${id}_help` : false
}

/**
 *
 * @param {Boolean} required
 * @param {String} initialClassName
 * @returns {string}
 */
export function getLabelClassName (required, initialClassName = '') {
  let className = initialClassName

  if (required) {
    className += ' required'
  }

  return className
}

/**
 *
 * @param {String|undefined} error
 * @param initialClassName
 * @returns {string}
 */
export function getInputClassName (error, initialClassName = 'form-control') {
  let className = initialClassName

  if (error) {
    className += ' is-invalid'
  }

  return className
}

/**
 *
 * @returns {string|undefined}
 */
export function getError (rules, key, value) {
  if (rules.length === 0) {
    return undefined
  }

  const rulesInKey = rules[key]
  if (rulesInKey === undefined || rulesInKey.length === 0) {
    return undefined
  }

  let error = ''
  for (let rule of rulesInKey) {
    error = rule.check(value)
    if (error !== undefined) {
      break
    }
  }

  return error
}
