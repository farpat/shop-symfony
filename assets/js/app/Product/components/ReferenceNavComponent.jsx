import React from "react"
import {connect} from "react-redux"
import PropTypes from 'prop-types'
import ReferenceNavItemComponent from "./ReferenceNavItemComponent"

class ReferenceNavComponent extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        return (
            <nav className="nav-product-reference">
                {
                    this.props.references.map(reference =>
                        <ReferenceNavItemComponent reference={reference} key={reference.id}/>
                    )
                }
            </nav>
        )
    }
}

ReferenceNavComponent.propTypes = {
    references: PropTypes.arrayOf(PropTypes.shape({
        id: PropTypes.number.isRequired
    }))
}

const mapStateToProps = (state) => {
    return {
        references: state.productReducer.productReferences
    }
}

const mapDispatchToProps = (dispatch) => {
    return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(ReferenceNavComponent)
