import React, { useEffect, useState } from 'react'
import { jsonGet } from '@farpat/api'

function CategoryManagement () {
  const [state, setState] = useState({
    information: {},
    isLoading  : true
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
    <div>
      <ul className="list-group">
        {
          state.information.categories.map((categoryItem, index) =>
            <CategoryItem categoryItem={categoryItem} key={categoryItem.category.id}/>
          )
        }
      </ul>
    </div>
  )
}

function CategoryItem ({ categoryItem }) {
  return <li className="list-group-item">
    <p>{categoryItem.category.nomenclature}</p>
    {
      categoryItem.children.length > 0 && <ul className="list-group">
        {
          categoryItem.children.map((childItem, index) =>
            <CategoryItem categoryItem={childItem} key={childItem.category.id}/>
          )
        }
      </ul>
    }
    <button className="btn btn-primary btn-sm my-2">Add a child</button>
  </li>
}

CategoryManagement.propTypes = {}

export default CategoryManagement
