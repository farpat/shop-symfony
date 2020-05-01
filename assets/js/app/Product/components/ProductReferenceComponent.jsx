import React from "react"
import PropTypes from 'prop-types'
import ReferenceSliderComponent from "./ReferenceSliderComponent"
import Str from "../../../src/String/Str"
import {connect} from "react-redux"

class ProductReferenceComponent extends React.Component {
    constructor(props) {
        super(props)
    }

    updateQuantity(reference, event) {
        this.props.updateQuantity(reference, event.target.value)
    }

    addInCart(reference) {
        this.props.addInCart(reference, this.getQuantity(reference))
    }

    getQuantity(reference) {
        return this.props.quantities[reference.id] || 1
    }

    getCartItem(reference) {
        return this.props.cartItems[reference.id]
    }


    render() {
        return (
            <article className="row">
                {
                    this.props.currentReference.mainImage &&
                    <div className="col-md-8">
                        <ReferenceSliderComponent currentReference={this.props.currentReference}
                                                  activatedIndexByReference={this.props.activatedSliderIndexByReference}/>
                    </div>
                }
                <div className="col-md">
                    <ul className="list-unstyled">
                        <li className="mb-5">
                            {
                                Str.toLocaleCurrency(this.props.currentReference.unitPriceIncludingTaxes, this.props.currency)
                            }
                        </li>
                        <li>
                            {
                                !this.getCartItem(this.props.currentReference) &&
                                <div>
                                    Quantity of &nbsp;
                                    <input type="number" style={{maxWidth: 75}} min={1}
                                           value={this.getQuantity(this.props.currentReference)}
                                           onChange={this.updateQuantity.bind(this, this.props.currentReference)}/>

                                    <button className="btn btn-primary btn-sm ml-3"
                                            onClick={this.addInCart.bind(this, this.props.currentReference)}>
                                        <i className="fa fa-shopping-cart"></i> Add in cart
                                    </button>
                                </div>
                            }
                            {
                                this.getCartItem(this.props.currentReference) &&
                                <div>
                                    Already in cart, quantity : {this.getCartItem(this.props.currentReference).quantity}
                                </div>
                            }
                        </li>
                    </ul>
                </div>
            </article>
        )
    }
}

ProductReferenceComponent.propTypes = {
    currentReference:                PropTypes.shape({
        id:                      PropTypes.number.isRequired,
        label:                   PropTypes.string.isRequired,
        mainImage:               PropTypes.shape({
            urlThumbnail: PropTypes.string.isRequired,
            altThumbnail: PropTypes.string.isRequired
        }),
        unitPriceIncludingTaxes: PropTypes.number.isRequired
    }),
    currency:                        PropTypes.string.isRequired,
    activatedSliderIndexByReference: PropTypes.object.isRequired,
    quantities:                      PropTypes.object.isRequired,
    cartItems:                       PropTypes.object.isRequired,

    updateQuantity: PropTypes.func.isRequired,
    addInCart:      PropTypes.func.isRequired
}

const mapStateToProps = (state) => {
    return {
        activatedSliderIndexByReference: state.productReducer.activatedSliderIndexByReference,
        quantities:                      state.cartReducer.quantities,
        cartItems:                       state.cartReducer.cartItems
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        updateQuantity: (reference, quantity) => dispatch({type: 'UPDATE_QUANTITY', reference, quantity}),
        addInCart:      (reference, quantity) => dispatch({type: 'ADD_IN_CART', reference, quantity})
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(ProductReferenceComponent)
