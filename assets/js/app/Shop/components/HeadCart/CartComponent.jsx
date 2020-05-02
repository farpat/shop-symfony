import React from "react"
import {hot} from "react-hot-loader/root"
import PropTypes from 'prop-types'
import {connect} from "react-redux"
import ItemComponent from "./Cart/ItemComponent"
import TotalComponent from "./Cart/TotalComponent"

class CartComponent extends React.Component {
    constructor(props) {
        super(props)
    }

    getItem(referenceId) {
        return this.props.items[referenceId]
    }

    render() {
        const referenceIds = Object.keys(this.props.items)

        return (
            <div>
                {
                    referenceIds.length === 0 &&
                    <div className="nav-link"><i className="fas fa-shopping-cart"/></div>
                }

                {

                    referenceIds.length > 0 &&
                    <div>
                        <button aria-expanded="false" aria-haspopup="true"
                                className="nav-link btn btn-link dropdown-toggle mr-md-2"
                                data-toggle="dropdown" id="button-cart">
                            <i className="fas fa-shopping-cart"/> - {referenceIds.length}
                        </button>
                        <div aria-labelledby="button-cart" className="dropdown-menu dropdown-menu-right header-cart">
                            <table className="table table-borderless table-hover">
                                <tbody>
                                {
                                    referenceIds.map(referenceId =>
                                        <ItemComponent item={this.getItem(referenceId)} key={referenceId}/>
                                    )
                                }
                                </tbody>

                                <TotalComponent items={this.props.items}/>
                            </table>
                        </div>
                    </div>
                }
            </div>
        )
    }
}

CartComponent.propTypes = {
    items: PropTypes.object.isRequired
}

const mapStateToProps = (state) => {
    return {
        items: state.cart.items
    }
}

const mapDispatchToProps = (dispatch) => {
    return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(hot(CartComponent))