export function changeValue (productFieldKey, updateFilter, event) {
  updateFilter(productFieldKey, event.target.value)
}

export function getValue(filters, key) {
  return filters[key] || ''
}