import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'

function ReferenceNavItemComponent ({ reference, currentReference, setCurrentReference }) {
  const getLiClass = function () {
    let className = 'nav-product-reference-item'
    if (reference === currentReference) {
      className += ' bg-primary'
    }
    return className
  }

  const getTitleClass = function () {
    let className = 'nav-product-reference-item-title'
    if (reference === currentReference) {
      className += ' text-white'
    }
    return className
  }

  return (
    <div className={getLiClass()}>
      <a
        className='nav-product-reference-item-container'
        onClick={(event) => {
          event.preventDefault()
          setCurrentReference(reference)
        }}
      >
        {
          reference.mainImage &&
          <img
            src={reference.mainImage.urlThumbnail}
            alt={reference.mainImage.altThumbnail}
          />
        }
        <h2 className={getTitleClass()}>{reference.label}</h2>
      </a>
    </div>
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
