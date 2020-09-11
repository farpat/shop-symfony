import React, { useEffect, useState } from 'react'
import { jsonGet } from '@farpat/api'
import AddForm from './AddForm'
import EditForm from './EditForm'
import PropTypes from 'prop-types'

function CategoriesManagement () {
  const [state, setState] = useState({
    information: {},
    isLoading  : true,
    editing    : null,
    adding     : null,
  })

  useEffect(() => {
    (async function () {
      const response = await jsonGet('/api/profile/admin/categories')
      setState({
        ...state,
        information: response,
        isLoading  : false
      })
    })()
  }, [])

  if (state.isLoading) {
    return <div className="text-center mt-5">
      <i className='fas fa-spinner spinner fa-7x'/>
    </div>
  }

  return (
    <div className="category-management">
      <h1>Categories management</h1>
      <ul className="list-group-flush">
        {
          state.information.categories.map(categoryItem =>
            <CategoryItem categoryItem={categoryItem} key={categoryItem.category.id} isFirstLevel={true}
                          state={state} setState={setState}
            />
          )
        }
      </ul>
    </div>
  )
}

CategoriesManagement.propTypes = {}

function CategoryItem ({ categoryItem, isFirstLevel = false, setState, state }) {
  const hasChildren = categoryItem.children.length > 0
  const isEditing = categoryItem === state.editing
  const isAdding = categoryItem === state.adding
  const isDisplayingButtons = !state.editing && !state.adding
  let wrapperClassName = 'list-group-item'
  if (isFirstLevel) {
    wrapperClassName += ' list-group-item-1'
  }

  const setAdding = function (categoryItem) {
    setState({ ...state, adding: categoryItem, editing: null })
  }

  const setEditing = function (categoryItem) {
    setState({ ...state, adding: null, editing: categoryItem })
  }

  const cancelAddingOrEditing = function () {
    setState({ ...state, adding: null, editing: null })
  }

  const setCategoryItems = function (categoryItems) {
    setState({
      ...state,
      information: { ...state.information, categories: categoryItems },
      editing    : false,
      adding     : false
    })
  }

  return <li className={wrapperClassName}>
    <span className="category-management-nomenclature">{categoryItem.category.nomenclature}</span>
    {
      isDisplayingButtons &&
      <>
        <Button type="Edit" onClick={() => setEditing(categoryItem)}></Button>
        <Button type="Delete"
                onClick={() => window.confirm(`Do you confirm the deletion of << ${categoryItem.category.nomenclature} >> ?`)}></Button>
      </>
    }
    {
      (hasChildren || isAdding || isEditing) && <ul className="list-group">
        {
          isEditing && <li className="list-group-item list-group-item-primary shadow-sm">
            <EditForm categoryItem={categoryItem} setCategoryItems={(categoryItems) => setCategoryItems(categoryItems)}
                      cancelEditingOrAdding={cancelAddingOrEditing}
            />
          </li>
        }
        {
          categoryItem.children.map(childItem =>
            <CategoryItem categoryItem={childItem} key={childItem.category.id}
                          state={state} setState={setState}
            />
          )
        }
        {
          isAdding && <li className="list-group-item list-group-item-success shadow-sm mb-3">
            <AddForm cancelEditingOrAdding={cancelAddingOrEditing} categoryItem={categoryItem}
                     setCategoryItems={(categoryItems) => setCategoryItems(categoryItems)}
            />
          </li>
        }
      </ul>
    }

    {
      isDisplayingButtons && <Button type="Add" onClick={() => setAdding(categoryItem)}>Add a child</Button>
    }
  </li>
}

CategoryItem.prototype = {
  categoryItem: PropTypes.shape({
    category: PropTypes.shape({
      id          : PropTypes.number.isRequired,
      nomenclature: PropTypes.string.isRequired,
      description : PropTypes.string.isRequired
    }),
    children: PropTypes.arrayOf(PropTypes.shape({
      id          : PropTypes.number.isRequired,
      nomenclature: PropTypes.string.isRequired,
      description : PropTypes.string.isRequired
    }))
  })
}

function Button ({ type, children, onClick }) {
  return <button className={'category-management-btn-' + type.toLowerCase()} onClick={onClick}>
    {children || type}
  </button>
}

Button.prototype = {
  type    : PropTypes.string.isRequired,
  children: PropTypes.string,
  onClick : PropTypes.func.isRequired,
}

export default CategoriesManagement