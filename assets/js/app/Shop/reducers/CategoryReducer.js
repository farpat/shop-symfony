import categoryService from '../services/CategoryService'

export default (state = {}, action) => {
  switch (action.type) {
    case 'UPDATE_FILTER':
      return categoryService.updateFilter(action.key, action.value).getData()
    case 'UPDATE_PAGE':
      return categoryService.updatePage(action.page).getData()
    default:
      return categoryService.getData()
  }
}
