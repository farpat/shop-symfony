import React from "react"
import {hot} from "react-hot-loader/root"
import {connect} from "react-redux"
import PropTypes from 'prop-types'
import ReferenceNavComponent from "./ReferenceNavComponent"
import ProductReferenceComponent from "./ProductReferenceComponent"

class ProductComponent extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        return (
            <div>
                <ReferenceNavComponent/>

                <ProductReferenceComponent currentReference={this.props.currentReference}
                                           currency={this.props.currency}/>
            </div>
        )
    }
}

ProductComponent.propTypes = {
    currentReference: PropTypes.shape({
        id:                      PropTypes.number.isRequired,
        label:                   PropTypes.string.isRequired,
        mainImage:               PropTypes.shape({
            urlThumbnail: PropTypes.string.isRequired,
            altThumbnail: PropTypes.string.isRequired
        }),
        unitPriceIncludingTaxes: PropTypes.number.isRequired
    }),
    currency:         PropTypes.string.isRequired
}

const mapStateToProps = (state) => {
    return {
        currentReference: state.productReducer.currentReference,
        currency:         state.productReducer.currency
    }
}

const mapDispatchToProps = (dispatch) => {
    return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(hot(ProductComponent))
