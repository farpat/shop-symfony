import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'

function ReferenceNavItemComponent ({ reference, currentReference, changeReferenceInNav }) {
  const getItemClass = function () {
    let className = 'nav-product-reference-item'
    if (reference === currentReference) {
      className += ' selected'
    }
    return className
  }

  const getHref = function (reference) {
    return window.location.href.replace(/(#\d+)$/, `#${reference.id}`)
  }

  return (
    <a
      href={getHref(reference)}
      className={getItemClass()}
      onClick={(event) => {
        event.preventDefault()
        changeReferenceInNav(reference)
      }}
    >
      {
        reference.main_image && <img className="nav-product-reference-image" src={reference.main_image.url_thumbnail}
                                    alt={reference.main_image.alt_thumbnail}/>
      }

      <h2 className="nav-product-reference-title">{reference.label}</h2>
    </a>
  )
}

ReferenceNavItemComponent.propTypes = {
  reference       : PropTypes.shape({
    id       : PropTypes.number.isRequired,
    label    : PropTypes.string.isRequired,
    main_image: PropTypes.shape({
      url_thumbnail: PropTypes.string.isRequired,
      alt_thumbnail: PropTypes.string.isRequired
    })
  }),
  currentReference: PropTypes.shape({
    id   : PropTypes.number.isRequired,
    label: PropTypes.string.isRequired
  }),

  changeReferenceInNav: PropTypes.func.isRequired
}

const mapStateToProps = (state) => {
  return {
    currentReference: state.product.currentReference
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    changeReferenceInNav: (reference) => dispatch({ type: 'CHANGE_REFERENCE_IN_NAV', reference })
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(ReferenceNavItemComponent)
