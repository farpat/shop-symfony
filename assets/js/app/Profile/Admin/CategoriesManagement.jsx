import React, { createRef, useEffect, useState } from 'react'
import { jsonGet, jsonPost, jsonPut } from '@farpat/api'
import TextComponent from '../../ui/Form/TextComponent'

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

function EditForm ({ categoryItem, cancelEditingOrAdding, setCategoryItems }) {
  const [prefix, ...suffix] = categoryItem.category.nomenclature.split('.')
  const labelClassName = suffix.length > 0 ? '' : ' d-none'
  const startNomenclature = suffix.length > 0 ? prefix : ''
  const endNomenclature = suffix.length > 0 ? suffix.join('.') : prefix
  const endNomenclatureRef = createRef()

  const [state, setState] = useState({
    category    : {
      id          : categoryItem.category.id,
      nomenclature: categoryItem.category.nomenclature,
      label       : categoryItem.category.label,
      description : categoryItem.category.description,
    },
    isSubmitting: false
  })

  const onUpdate = function (name, value, startNomenclature) {
    if (name === 'nomenclature') {
      value = (startNomenclature !== '' ? startNomenclature + '.' : '') + value.replaceAll('.', '')
    }

    setState({ ...state, category: { ...state.category, [name]: value } })
  }

  const onSubmit = async (event) => {
    if (state.isSubmitting) {
      return
    }

    try {
      event.preventDefault()
      setState({ ...state, isSubmitting: true })
      const categories = await jsonPut(`/api/profile/admin/categories/${state.category.id}/edit`, state.category)
      setCategoryItems(categories)
      setState({ ...state, isSubmitting: false })
    } catch (error) {
      console.error(error)
    }
  }

  return <form onSubmit={onSubmit}>
    <div className="row align-items-center g-1 m-0 mb-3">
      <label className={'col-auto m-0' + labelClassName} htmlFor={'nomenclature'}>{startNomenclature}.</label>
      <div className="col m-0">
        <TextComponent wrapperclassName={'m-0'} id={'nomenclature'} name={'nomenclature'} ref={endNomenclatureRef}
                       value={endNomenclature} onUpdate={(name, value) => onUpdate(name, value, startNomenclature)}
                       attr={{ placeholder: 'Nomenclature' }}
        />
      </div>
    </div>

    <TextComponent id={'label'} label="Label" name={'label'} attr={{ placeholder: 'Label' }}
                   value={categoryItem.category.label} onUpdate={onUpdate}/>

    <TextComponent id={'description'} label="Description" name={'description'} attr={{ placeholder: 'Description' }}
                   value={categoryItem.category.description} onUpdate={onUpdate}/>

    <button className="btn btn-link" type="button" onClick={cancelEditingOrAdding}>Cancel</button>
    {
      state.isSubmitting ?
        <button className="btn btn-primary" disabled={true} type="submit">Editing...</button> :
        <button className="btn btn-primary" type="submit">Edit</button>
    }
  </form>
}

function AddForm ({ categoryItem, cancelEditingOrAdding, setCategoryItems }) {
  const onSubmit = async (event) => {
    event.preventDefault()
    try {
      const categories = await jsonPost(`/api/profile/admin/categories/new`, item)
      setCategoryItems(categories)
    } catch (error) {
      console.error(error)
    }
  }

  return <form onSubmit={onSubmit}>
    <div className="row align-items-center g-1 m-0 mb-3">
      <label className="col-auto m-0" htmlFor={'nomenclature'}>  {categoryItem.category.nomenclature}.</label>
      <div className="col m-0">
        <TextComponent wrapperclassName={'m-0'} id={'nomenclature'} name={'nomenclature'}
                       attr={{ placeholder: 'Nomenclature' }}/>
      </div>
    </div>

    <TextComponent id={'label'} label="Label" name={'label'} attr={{ placeholder: 'Label' }}/>

    <TextComponent id={'description'} label="Description" name={'description'}
                   attr={{ placeholder: 'Description' }}/>

    <button className="btn btn-link" onClick={cancelEditingOrAdding}>Cancel</button>
    <button className="btn btn-success">Add</button>
  </form>
}

function Button ({ type, children, onClick }) {
  return <button className={'category-management-btn-' + type.toLowerCase()} onClick={onClick}>
    {children || type}
  </button>
}

CategoriesManagement.propTypes = {}

export default CategoriesManagement
