import debounce from 'lodash/debounce'

export default class Input {
  constructor (element, options) {
    this.element = element
    this.options = options

    this.setContainerElement()
    this.setAutocompleteAttribute()
    this.cache = {}
    this.lastValue = ''
    this.updateScreenOnResize = debounce((e) => {
      this.updateScreen(e, null)
    }, 500)

    window.addEventListener('resize', this.updateScreenOnResize.bind(this))

    this.setOnMouseLeaveAnElement()
    this.setOnMouseHoverAnElement()
    this.setOnMouseDownAnElement()

    this.element.addEventListener('blur', this.blurHandler.bind(this))
    this.element.addEventListener('keyup', this.keyupHandler.bind(this))
    this.element.addEventListener('keydown', this.keydownHandler.bind(this))
    this.element.addEventListener('focus', this.focusHandler.bind(this))
  }

  setContainerElement () {
    this.containerElement = document.createElement('div')
    this.containerElement.classList.add('autocomplete-suggestions')
    if (this.options.menuClass !== undefined) {
      this.containerElement.classList.add(this.options.menuClass)
    }

    document.body.appendChild(this.containerElement)
  }

  setAutocompleteAttribute () {
    this.oldAutocompleteAttribute = this.element.getAttribute('autocomplete')
    this.element.setAttribute('autocomplete', 'off')
  }

  updateScreen (resizeEvent, nextElement) {
    var rect = this.element.getBoundingClientRect()
    this.containerElement.style.left = `${Math.round(rect.left + window.pageXOffset + this.options.offsetLeft)}px`
    this.containerElement.style.top = `${Math.round(rect.bottom + window.pageYOffset + this.options.offsetTop)}px`
    this.containerElement.style.width = `${Math.round(rect.right - rect.left)}px` // outerWidth

    if (resizeEvent === null) {
      this.containerElement.style.display = 'block'
      if (!this.containerElement.maxHeight) {
        this.containerElement.maxHeight = parseInt(getComputedStyle(this.containerElement, null).maxHeight)
      }
      if (!this.containerElement.suggestionHeight) {
        this.containerElement.suggestionHeight = this.containerElement.querySelector('.autocomplete-suggestion').offsetHeight
      }
      if (this.containerElement.suggestionHeight) {
        if (nextElement === null) {
          this.containerElement.scrollTop = 0
        } else {
          var scrTop = this.containerElement.scrollTop
          var selTop = nextElement.getBoundingClientRect().top - this.containerElement.getBoundingClientRect().top
          if (selTop + this.containerElement.suggestionHeight - this.containerElement.maxHeight > 0) {
            this.containerElement.scrollTop = selTop + this.containerElement.suggestionHeight + scrTop - this.containerElement.maxHeight
          } else if (selTop < 0) {
            this.containerElement.scrollTop = selTop + scrTop
          }
        }
      }
    }
  };

  setOnMouseLeaveAnElement () {
    var context = this.containerElement || document

    context.addEventListener('mouseleave', (e) => {
      var selectedItem = context.querySelector('.autocomplete-suggestion.selected')

      if (selectedItem !== null) {
        selectedItem.classList.remove('selected')
      }
    })
  }

  setOnMouseHoverAnElement () {
    var context = this.containerElement || document

    context.addEventListener('mouseover', (e) => {
      var found; var el = e.target

      while (el && !(found = el.classList.contains('autocomplete-suggestion'))) el = el.parentElement
      // test with `found = el.closest('.autocomplete-suggestion')`
      if (found) {
        var selectedItem = context.querySelector('.autocomplete-suggestion.selected')
        if (selectedItem !== null) {
          selectedItem.classList.remove('selected')
        }
        el.classList.add('selected')
      }
    })
  }

  setOnMouseDownAnElement () {
    var context = this.containerElement || document

    context.addEventListener('mousedown', (e) => {
      var targetElement = e.target

      if (targetElement.classList.contains('autocomplete-suggestion')) {
        var value = targetElement.getAttribute('data-val')
        this.element.value = value
        this.options.onSelect(e, value, targetElement)
        this.containerElement.style.display = 'none'
      }
    })
  }

  blurHandler () {
    var hoveredElement = document.querySelector('.autocomplete-suggestions:hover')

    if (hoveredElement === null) {
      this.lastValue = this.element.value
      this.containerElement.style.display = 'none'
    }
  }

  suggest (data) {
    var val = this.element.value
    this.cache[val] = data

    if (data.length > 0 && val.length >= this.options.minChars) {
      this.containerElement.innerHTML = data.reduce((acc, item) => {
        acc += this.options.renderItem(item, val)
        return acc
      }, '')

      this.updateScreen(null, null)
    } else {
      this.containerElement.style.display = 'none'
    }
  }

  keydownHandler (e) {
    if ((e.key === 'ArrowDown' || e.key === 'ArrowUp') && this.containerElement.innerHTML) {
      var next; var sel = this.containerElement.querySelector('.autocomplete-suggestion.selected')
      if (!sel) {
        next = (e.key === 'ArrowDown')
          ? this.containerElement.querySelector('.autocomplete-suggestion')
          : this.containerElement.children[this.containerElement.children.length - 1] // first : last
        next.classList.add('selected')
        this.element.value = next.getAttribute('data-val')
      } else {
        next = (e.key === 'ArrowDown')
          ? sel.nextElementSibling
          : sel.previousElementSibling

        if (next !== undefined) {
          sel.className = sel.className.replace('selected', '')
          next.classList.add('selected')
          this.element.value = next.getAttribute('data-val')
        } else {
          sel.className = sel.className.replace('selected', '')
          this.element.value = this.lastValue
          next = null
        }
      }
      this.updateScreen(null, next)
      return false
    } else if (e.key === 'Escape') {
      this.element.value = this.lastValue
      this.containerElement.style.display = 'none'
    } else if (e.key === 'Enter') {
      var sel = this.containerElement.querySelector('.autocomplete-suggestion.selected')
      if (sel && this.containerElement.style.display != 'none') {
        this.options.onSelect(e, sel.getAttribute('data-val'), sel)
        setTimeout(function () {
          this.containerElement.style.display = 'none'
        }, 20)
      }
    }
  }

  keyupHandler (e) {
    var key = e.key

    if (!key || key !== 'Enter' && key !== 'Escape') {
      var val = this.element.value

      if (val.length >= this.options.minChars) {
        if (val !== this.lastValue) {
          this.lastValue = val
          clearTimeout(this.timer)
          if (this.options.cache) {
            if (val in this.cache) {
              this.suggest(this.cache[val])
              return
            }

            // no requests if previous suggestions were empty
            for (var i = 1; i < val.length - this.options.minChars; i++) {
              var part = val.slice(0, val.length - i)
              if (part in this.cache && !this.cache[part].length) {
                this.suggest([])
                return
              }
            }
          }
          this.timer = setTimeout(() => {
            this.options.source(val, this.suggest.bind(this))
          }, this.options.delay)
        }
      } else {
        this.lastValue = val
        this.containerElement.style.display = 'none'
      }
    }
  }

  focusHandler (e) {
    this.lastValue = '\n'
    this.keyupHandler(e)
  }

  destroy () {
    window.removeEventListener('resize', this.updateScreenOnResize)
    this.element.removeEventListener('blur', this.blurHandler)
    this.element.removeEventListener('focus', this.focusHandler)
    this.element.removeEventListener('keydown', this.keydownHandler)
    this.element.removeEventListener('keyup', this.keyupHandler)

    if (this.oldAutocompleteAttribute) {
      this.element.setAttribute('autocomplete', this.oldAutocompleteAttribute)
    } else {
      this.element.removeAttribute('autocomplete')
    }

    document.body.removeChild(this.containerElement)
    this.element = null
  }
}
