import Arr from './Arr'

const units = ['o', 'Ko', 'Mo', 'Go', 'To', 'Po', 'Eo', 'Zo', 'Yo']

/**
 * @static {Object} parseKeysCache
 */
class Str {
  constructor () {
    Str.parseKeysCache = {}
  }

  isNumeric (number) {
    return !isNaN(number)
  }

  transformKeysToStar (str) {
    const keys = this.parseKeysInString(str)

    if (typeof keys === 'string') {
      return str
    }

    let transformedKey = keys[0]
    for (let i = 1; i + 1 < keys.length; i++) {
      transformedKey += '.*'
    }

    const lastKey = Arr.last(keys)
    transformedKey += (this.isNumeric(lastKey)) ? '.*' : `.${lastKey}`

    return transformedKey
  }

  parseKeysInString (str) {
    if (!Str.parseKeysCache.hasOwnProperty(str)) {
      const keys = Array.from(str.matchAll(/\[?([\w_-]+)\]?/g))

      Str.parseKeysCache[str] = keys.length === 1
        ? keys[0][1]
        : keys.map(key => key[1])
    }

    return Str.parseKeysCache[str]
  }

  formatCardNumber (text) {
    text = text.replace(/\s+/g, '').replace(/[^0-9]/gi, '')

    const matches = text.match(/\d{4,16}/g)
    const match = matches && matches[0] || ''
    const parts = []
    for (let i = 0, len = match.length; i < len; i += 4) {
      parts.push(match.substring(i, i + 4))
    }

    if (parts.length > 0) {
      text = parts.join(' ')
    }

    return text
  }

  /**
   *
   * @param {number} amount
   * @param {string} currency
   * @returns {string}
   */
  toLocaleCurrency (amount, currency) {
    if (typeof amount === 'string') {
      amount = parseFloat(amount)
    }

    return amount.toLocaleString(undefined, { style: 'currency', currency })
  }

  toLocaleNumber (value) {
    if (value === null) {
      return ''
    }

    return parseFloat(value).toLocaleString(undefined, {
      maximumFractionDigits: 2
    })
  }

  bytesToSize (bytes) {
    let i = 0
    while (bytes >= 1024) {
      bytes /= 1024
      ++i
    }

    return `${bytes.toFixed(2)} ${units[i]}`
  }

  sizeToBytes (size) {
    const sizes = size.split(' ')
    let bytes = sizes[0]
    let i = 0
    while (units[i] && units[i] !== sizes[1]) {
      bytes *= 1024
      ++i
    }

    return bytes
  }

  markValueIntoText (neddle, haystack) {
    neddle = neddle.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&')
    const regex = new RegExp(`(${neddle.split(' ').join('|')})`, 'gi')
    return haystack.replace(regex, '<mark>$1</mark>')
  }

  /**
   *
   * @param {string} hexa
   * @return {{red: number, green: number, blue:number}}
   */
  hexaToRgb (hexa) {
    hexa = hexa.substring(1, 7)

    const red = parseInt(hexa.substring(0, 2), 16)
    const green = parseInt(hexa.substring(2, 4), 16)
    const blue = parseInt(hexa.substring(4, 6), 16)

    return { red, green, blue }
  }

  /**
   *
   * @param {string} currentQueryString
   * @param {string} key
   * @param {string} value
   */
  addQueryString (currentQueryString, key, value) {
    const prefix = currentQueryString.length === 0 ? '?' : '&'
    return `${prefix + key}=${value}`
  }

  /**
   *
   * @param {Object} object
   * @returns {string}
   */
  dump (object) {
    if (typeof object !== 'object') {
      return `<pre>${object}</pre>`
    }

    let json = JSON.stringify(object, undefined, 4)
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    return `<pre>${json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
      let cls = 'number'
      if (/^"/.test(match)) {
        cls = /:$/.test(match) ? 'key' : 'string'
      } else if (/true|false/.test(match)) {
        cls = 'boolean'
      } else if (/null/.test(match)) {
        cls = 'null'
      }
      return `<span class="${cls}">${match}</span>`
    })}</pre>`
  }
}

export default new Str()
