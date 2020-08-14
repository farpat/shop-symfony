import React, { useEffect, useState } from 'react'
import { hot } from 'react-hot-loader/root'
import { jsonGet } from '@farpat/api'
import { HashRouter as Router, NavLink as Link, Route, Switch, useRouteMatch } from 'react-router-dom'

function App () {
  const [navigations, setNavigations] = useState({ user: [], admin: [] })
  const [isSelected, setIsSelected] = useState(false)

  useEffect(() => {
    (async function () {
      const response = await jsonGet('/profile-api/navigation')
      setNavigations(response)
      if (window.location.hash !== '#/') {
        setIsSelected(true)
      }
    })()
  }, [])

  return <Router>
    {
      isSelected ?
        <Link to='/' className="d-block mb-5" onClick={() => setIsSelected(false)}>&larr; back to home</Link> :
        <Navigation navigations={navigations} setIsSelected={setIsSelected}/>
    }


    <Switch>
      {
        navigations.user.map(route => {
          const Component = require(`./${route.component}`).default
          return <Route key={route.path} path={route.path}>
            <Component/>
          </Route>
        })
      }
    </Switch>
  </Router>
}

function Navigation ({ navigations, setIsSelected }) {
  return <>
    <h2>My profile</h2>
    <nav className="statistics">
      {
        navigations.user.map(navigationItem =>
          <NavigationItem key={navigationItem.path} navigationItem={navigationItem} setIsSelected={setIsSelected}/>
        )
      }
    </nav>

    {
      navigations.admin.length > 0 &&
      <>
        <h2 className="mt-5">Admin</h2>

        <nav className="statistics">
          {
            navigations.admin.map(navigationItem =>
              <NavigationItem key={navigationItem.path} navigationItem={navigationItem} setIsSelected={setIsSelected}/>
            )
          }
        </nav>
      </>
    }
  </>
}

function NavigationItem ({ navigationItem, setIsSelected }) {
  const isCurrentRoute = useRouteMatch({ path: navigationItem.path, exact: true })

  return <Link
    to={navigationItem.path} isActive={() => isCurrentRoute !== null} activeClassName="active"
    onClick={() => setIsSelected(true)}
    className={`statistic bg-${navigationItem.color}`}
  >
    <h2 className="statistic-title">
      <i className={`statistic-icon ${navigationItem.icon}`}/> {navigationItem.label}
    </h2>
    <p className="statistic-value">{navigationItem.value}&nbsp;</p>
  </Link>
}

export default hot(App)
