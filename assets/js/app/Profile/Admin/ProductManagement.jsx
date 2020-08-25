import React, { useEffect, useState } from 'react'
import { jsonGet } from '@farpat/api'
import Dump from '../../ui/Dump'

function ProductManagement (props) {
  const [state, setState] = useState({
    information: {},
    isLoading  : true
  })

  useEffect(() => {
    (async function () {
      const response = await jsonGet('/api/profile/admin/products')
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
      <Dump object={state}/>
    </div>
  )
}

ProductManagement.propTypes = {}

export default ProductManagement
