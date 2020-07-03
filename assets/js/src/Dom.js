class Dom {
  /**
   *
   * @param {HTMLElement} containerElement
   * @param {HTMLElement} insideElement
   * @returns {boolean}
   */
  isInside (containerElement, insideElement) {
    let parentElement = insideElement
    while (parentElement) {
      if (parentElement === containerElement) {
        return true
      }
      parentElement = parentElement.parentElement
    }

    return false
  }
}

export default new Dom()
