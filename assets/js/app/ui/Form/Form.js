/**
 *
 * @param {Object} rulesInObject
 * @returns {Array}
 */

export function getRulesFromBack (rulesInObject) {
  if (rulesInObject === null) {
    return []
  }

  const rules = []
  for (const key in rulesInObject) {
    const rule = rulesInObject[key]
    const RuleClass = require(`../../../src/Security/Rules/${rule.type}Rule`).default
    rules.push(new RuleClass(rule.parameters))
  }

  return rules
}

/**
 *
 * @param {string} help
 * @param {string} id
 * @returns {boolean|string}
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
  for (const rule of rulesInKey) {
    error = rule.check(value)
    if (error !== undefined) {
      break
    }
  }

  return error
}
