import React from "react"
import {hot} from "react-hot-loader/root"
import PropTypes from 'prop-types'
import {connect} from "react-redux"

class HeadCartComponent extends React.Component {
    constructor(props) {
        super(props)
    }

    getItem(itemKey) {
        return this.props.items[itemKey]
    }

    render() {
        return (
            <ul>
                {
                    Object.keys(this.props.items).map(itemKey =>
                        <li key={this.getItem(itemKey).id}>
                            Qty: {this.getItem(itemKey).quantity}
                            Label: {this.getItem(itemKey).reference.label}
                        </li>
                    )
                }
            </ul>
        )
    }
}

HeadCartComponent.propTypes = {
    items: PropTypes.object.isRequired
}

const mapStateToProps = (state) => {
    return {
        items: state.items
    }
}

const mapDispatchToProps = (dispatch) => {
    return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(hot(HeadCartComponent))
