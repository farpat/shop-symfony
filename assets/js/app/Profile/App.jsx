import React, { useEffect, useState } from 'react'
import { hot } from 'react-hot-loader/root'
import { jsonGet } from '@farpat/api'
import { HashRouter as Router, NavLink as Link, Route, Switch, useRouteMatch } from 'react-router-dom'

function App () {
  const [routes, setRoutes] = useState([])

  useEffect(() => {
    (async function () {
      const response = await jsonGet('/profile-api/navigation')
      setRoutes(response)
    })()
  }, [])

  return <Router>
    <>
      <Navigation routes={routes}/>

      <hr/>

      <Switch>
        {
          routes.map(route => {
            const Component = require(`./${route.component}`).default
            return <Route exact key={route.path} path={route.path}>
              <Component/>
            </Route>
          })
        }
      </Switch>
    </>
  </Router>
}

function Navigation ({ routes }) {
  return (
    <nav className="nav nav-pills justify-content-center">
      {
        routes.map(route => <NavigationLink key={route.path} to={route.path} label={route.label}/>)
      }
    </nav>
  )
}

function NavigationLink ({ to, label }) {
  const isCurrentRoute = useRouteMatch({ path: to, exact: true })

  return (
    <Link to={to} className="nav-link" activeClassName="active" isActive={() => isCurrentRoute !== null}>
      {label}
    </Link>
  )
}

export default hot(App)
