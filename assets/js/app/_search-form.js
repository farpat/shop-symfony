import AutoComplete from '../src/Autocomplete'
import Requestor from '@farpat/api'
import Str from '../src/String/Str'

const inputContainer = document.querySelector('#form-search')
const input = inputContainer.querySelector('input')

const renderProduct = function (item, searchValue) {
  const text = Str.markValueIntoText(searchValue, item.label)
  const autocompleteImgString = item.image ? `<div class="autocomplete-img"><img src="${item.image}" alt="${item.label}"></div>` : ''

  return `
<div data-url="${item.url}" class="autocomplete-suggestion" data-val="${searchValue}">${autocompleteImgString}
    <div class="autocomplete-description">
        <p class="autocomplete-description-label">${text}</p>
        <p class="autocomplete-description-price">From ${Str.toLocaleCurrency(item.minUnitPriceIncludingTaxes, 'EUR')}</p>
    </div>
</div>
`
}
const renderCategory = function (item, searchValue) {
  const text = Str.markValueIntoText(searchValue, item.label)
  const autocompleteImgString = item.image ? `<div class="autocomplete-img"><img src="${item.image}" alt="${item.label}"></div>` : ''

  return `
<div data-url="${item.url}" class="autocomplete-suggestion" data-val="${searchValue}">${autocompleteImgString}
    <div class="autocomplete-description">
        <p class="autocomplete-description-label">${text}</p>
    </div>
</div>
`
}
const renderNotItems = function () {
  return `
<div class="autocomplete-suggestion">
    <div class="autocomplete-description">
        <p class="autocomplete-description-label">no items</p>
    </div>
</div>
`
}

new AutoComplete({
  selector: input,
  minChars: 2,
  cache: true,
  source: function (q, suggest) {
    inputContainer.classList.add('searching')

    Requestor.newRequest()
      .get(input.dataset.url, { q })
      .then(response => {
        inputContainer.classList.remove('searching')

        const data = response.length > 0 ? response : [{ label: null }]
        suggest(data)
      })
  },
  renderItem: function (item, q) {
    if (item.label === null) {
      return renderNotItems()
    } else if (item.minUnitPriceIncludingTaxes !== undefined) {
      return renderProduct(item, q)
    } else {
      return renderCategory(item, q)
    }
  },
  onSelect: function (e, term, item) {
    if (item.dataset.url !== undefined) {
      window.location.href = item.dataset.url
    }
  }
})
