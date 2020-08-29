import React, { useEffect, useRef, useState } from 'react'
import { jsonGet } from '@farpat/api'
import Chart from 'chart.js'

function Visits () {
  const canvas = useRef(null)

  const [state, setState] = useState({
    visits   : [],
    isLoading: true,
  })

  useEffect(() => {
    (async function () {
      const response = await jsonGet('/api/profile/user/visits')
      setState({
        ...state,
        visits   : response,
        isLoading: false
      })
    })()
  }, [])

  useEffect(() => {
    if (!state.isLoading) {
      const labels = state.visits.map(visit => visit.url)
      const data = state.visits.map(visit => visit.count)
      canvas.current.height = data.length * 12.5
      const blue = getComputedStyle(document.body).getPropertyValue('--bs-blue').trim()

      new Chart(canvas.current, {
        type   : 'horizontalBar',
        data   : {
          labels,
          datasets: [{
            data,
            backgroundColor: blue + 'DD',
            borderColor    : blue,
          }]
        },
        options: {
          legend: { display: false },
          scales: {
            xAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }]
          }
        }
      })
    }
  }, [state.isLoading])

  if (state.isLoading) {
    return <div className="text-center mt-5">
      <i className='fas fa-spinner spinner fa-7x'/>
    </div>
  }

  return (
    <>
      <h1>Monthly visits</h1>
      <canvas ref={canvas}></canvas>
    </>
  )
}

export default Visits
