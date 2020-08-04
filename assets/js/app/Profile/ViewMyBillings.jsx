import React, { useEffect, useState } from 'react'
import { jsonGet } from '@farpat/api'

function ViewMyBillings () {
  const [state, setState] = useState({
    billings : [],
    isLoading: true,
  })

  useEffect(() => {
    (async function () {
      const response = await jsonGet('/profile-api/billings')
      setState({
        ...state,
        billings : response,
        isLoading: false
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
      <table className="table table-bordered">
        <thead>
        <tr>
          <th>Number</th>
          <th>Status</th>
          <th>Delivery address</th>
          <th></th>
        </tr>
        </thead>
        <tbody>
        {
          state.billings.map(billing => <tr key={billing.number}>
            <td>{billing.number}</td>
            <td>{billing.status}</td>
            <td>{billing.address}</td>
            <td>
              <a href={`/billings/view/${billing.number}`} className="btn btn-link" target="_blank">See</a>
              <a href={`/billings/export/${billing.number}`} className="btn btn-link">Download</a>
            </td>
          </tr>)
        }
        </tbody>
      </table>
    </div>
  )
}

export default ViewMyBillings
