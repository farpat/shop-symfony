import React, { useEffect, useState } from 'react'
import { jsonGet } from '@farpat/api'
import Str from '../../../src/Str'

function Billings () {
  const [state, setState] = useState({
    billings : [],
    isLoading: true,
  })

  useEffect(() => {
    (async function () {
      const response = await jsonGet('/api/profile/user/billings')
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

  return <>
    <h1>Billings</h1>
    <table className="table table-bordered">
      <thead>
      <tr>
        <th>Number</th>
        <th>Status</th>
        <th>Total price including taxes</th>
        <th>Delivery address</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      {
        state.billings.map(billing => <tr key={billing.number}>
          <td>{billing.number}</td>
          <td>{billing.status}</td>
          <td>{Str.toLocaleCurrency(billing.total_price_including_taxes, 'EUR')}</td>
          <td>{billing.address}</td>
          <td>
            <a href={`/billings/view/${billing.number}`} className="btn btn-link" target="_blank">See</a>|<a
            href={`/billings/export/${billing.number}`} className="btn btn-link" download>Download</a>
          </td>
        </tr>)
      }
      </tbody>
    </table>
  </>
}

export default Billings
