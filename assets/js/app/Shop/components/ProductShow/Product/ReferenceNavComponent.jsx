import React from 'react'
import { connect } from 'react-redux'
import PropTypes from 'prop-types'
import ReferenceNavItemComponent from './ReferenceNavItemComponent'

function ReferenceNavComponent ({ references }) {
  if (references.length <= 1) {
    return null
  }

  return (
    <nav className='nav-product-reference'>
      {
        references.map(reference =>
          <ReferenceNavItemComponent reference={reference} key={reference.id}/>
        )
      }
    </nav>
  )
}

ReferenceNavComponent.propTypes = {
  references: PropTypes.arrayOf(PropTypes.shape({
    id: PropTypes.number.isRequired
  }))
}

const mapStateToProps = (state) => {
  return {
    references: state.product.productReferences
  }
}

const mapDispatchToProps = (dispatch) => {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(ReferenceNavComponent)
