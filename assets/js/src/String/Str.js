import Arr from '../Array/Arr'

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
}

export default new Str()
