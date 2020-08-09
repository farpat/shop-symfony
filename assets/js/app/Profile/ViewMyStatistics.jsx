import React, { useEffect, useState } from 'react'
import { jsonGet } from '@farpat/api'

function ViewMyStatistics () {
  const [state, setState] = useState({
    statistics: [],
    isLoading : true,
  })

  useEffect(() => {
    (async function () {
      const response = await jsonGet('/profile-api/statistics')
      setState({
        ...state,
        statistics: response,
        isLoading : false
      })
    })()
  }, [])

  if (state.isLoading) {
    return <div className="text-center mt-5">
      <i className='fas fa-spinner spinner fa-7x'/>
    </div>
  }

  return <div className="statistics">
    {
      state.statistics.map((statistic, index) => <Statistic key={index} statistic={statistic}/>)
    }
  </div>

}

function Statistic ({ statistic }) {
  return <div className={`statistic bg-${statistic.color}`}>
    <h2 className="statistic-title">
      <i className={`statistic-icon fas fa-${statistic.icon}`}/>
      {statistic.label}
    </h2>
    <p className="statistic-value">{statistic.value}</p>
  </div>
}

export default ViewMyStatistics
