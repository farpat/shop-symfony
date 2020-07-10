import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'

function ReferenceNavItemComponent ({ reference, currentReference, setCurrentReference }) {
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
        setCurrentReference(reference)
      }}
    >
      {
        reference.mainImage && <img className="nav-product-reference-image" src={reference.mainImage.urlThumbnail} alt={reference.mainImage.altThumbnail}/>
      }

      <h2 className="nav-product-reference-title">{reference.label}</h2>
    </a>
  )
}

ReferenceNavItemComponent.propTypes = {
  reference       : PropTypes.shape({
    id       : PropTypes.number.isRequired,
    label    : PropTypes.string.isRequired,
    mainImage: PropTypes.shape({
      urlThumbnail: PropTypes.string.isRequired,
      altThumbnail: PropTypes.string.isRequired
    })
  }),
  currentReference: PropTypes.shape({
    id   : PropTypes.number.isRequired,
    label: PropTypes.string.isRequired
  }),

  setCurrentReference: PropTypes.func.isRequired
}

const mapStateToProps = (state) => {
  return {
    currentReference: state.product.currentReference
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    setCurrentReference: (reference) => dispatch({ type: 'UPDATE_REFERENCE', reference })
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(ReferenceNavItemComponent)
