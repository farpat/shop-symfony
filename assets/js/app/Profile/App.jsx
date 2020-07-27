import React from 'react'
import { hot } from 'react-hot-loader/root'
import { HashRouter as Router, Switch, Route, NavLink as Link, useRouteMatch } from 'react-router-dom'
import Home from './Home'
import UpdateMyInformations from './UpdateMyInformations'
import ViewMyBillings from './ViewMyBillings'
import UpdateMyAddresses from './UpdateMyAddresses'

const routes = [
  {
    path     : '/',
    component: Home,
    label    : 'Home'
  },
  {
    path     : '/update-my-informations',
    component: UpdateMyInformations,
    label    : 'Update my informations'
  },
  {
    path     : '/update-my-addresses',
    component: UpdateMyAddresses,
    label    : 'Update my addresses'
  },
  {
    path     : '/view-my-billings',
    component: ViewMyBillings,
    label    : 'View my billings'
  }
]

function App () {
  return (
    <Router>
      <>
        <Navigation/>

        <hr/>

        <Switch>
          {
            routes.map(route => {
              const Component = route.component
              return <Route exact key={route.path} path={route.path}>
                <Component/>
              </Route>
            })
          }
        </Switch>
      </>
    </Router>
  )
}

function Navigation () {
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
