import React, { useEffect, useState } from 'react'
import { hot } from 'react-hot-loader/root'
import { jsonGet } from '@farpat/api'
import { HashRouter as Router, NavLink as Link, Route, Switch, useRouteMatch } from 'react-router-dom'

function App () {
  const [state, setState] = useState({
    navigations: { user: [], admin: [] },
    isSelected : false,
    isLoading  : true
  })

  console.log('render')

  useEffect(() => {
    (async function () {
      if (!state.isSelected) {
        setState({
          ...state,
          navigation: { ...state.navigation, user: [], admin: [] }
        })

        const response = await jsonGet('/api/profile/navigation')
        setState({
          ...state,
          navigations: { ...state.navigation, ...response },
          isLoading  : false,
          isSelected : window.location.hash !== '' && window.location.hash !== '#/'
        })
      }
    })()
  }, [state.isSelected])

  const renderComponent = function (navigation) {
    return navigation.map(navigationItem => {
      const Component = require(`./${navigationItem.component}`).default
      return <Route key={navigationItem.path} path={navigationItem.path}>
        <Component/>
      </Route>
    })
  }

  const renderNavigation = function (navigationItems, h2) {
    if (navigationItems.length === 0) {
      return null
    }

    return <>
      <h2 className={h2.class}>{h2.title}</h2>
      <nav className="statistics">
        {
          navigationItems.map(navigationItem =>
            <NavigationItem key={navigationItem.path} navigationItem={navigationItem}
                            setIsSelected={setIsSelected}/>
          )
        }
      </nav>
    </>
  }

  const setIsSelected = function () {
    setState({ ...state, isSelected: true })
  }

  if (state.isLoading) {
    return <div className="text-center mt-5">
      <i className='fas fa-spinner spinner fa-7x'/>
    </div>
  }

  return <Router>
    {
      state.isSelected ?
        <Link to='/' className="d-block mb-5"
              onClick={() => setState({ ...state, isSelected: false, isLoading: true })}>
          &larr; back to home
        </Link> :
        <>
          {
            renderNavigation(state.navigations.user, { title: 'My profile', class: null })
          }
          {
            renderNavigation(state.navigations.admin, { title: 'Admin', class: 'mt-5' })
          }
        </>
    }

    <Switch>
      {
        renderComponent(state.navigations.user)
      }
      {
        renderComponent(state.navigations.admin)
      }
    </Switch>
  </Router>
}

function NavigationItem ({ navigationItem, setIsSelected }) {
  const isCurrentRoute = useRouteMatch({ path: navigationItem.path, exact: true })

  return <Link
    to={navigationItem.path}
    isActive={() => isCurrentRoute !== null} activeClassName="active"
    onClick={() => setIsSelected()}
    className={`statistic bg-${navigationItem.color}`}
  >
    <h2 className="statistic-title">
      <i className={`statistic-icon ${navigationItem.icon}`}/> {navigationItem.label}
    </h2>
    <p className="statistic-value">{navigationItem.value}&nbsp;</p>
  </Link>
}

export default hot(App)
