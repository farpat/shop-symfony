import React from "react";
import PropTypes from 'prop-types';
import ReferenceSliderComponent from "./ReferenceSliderComponent";
import Str from "../../../src/String/Str";
import {connect} from "react-redux";

class ProductReferenceComponent extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <article className="row">
                {
                    this.props.currentReference.mainImage &&
                    <div className="col-md-8">
                        <ReferenceSliderComponent currentReference={this.props.currentReference} activatedIndexByReference={this.props.activatedSliderIndexByReference}/>
                    </div>
                }
                <div className="col-md">
                    <ul className="list-unstyled">
                        <li className="mb-5">
                            {Str.toLocaleCurrency(this.props.currentReference.unitPriceIncludingTaxes, this.props.currency)}
                        </li>
                        <li>
                            Quantity of {this.props.currentReference.label}
                        </li>
                    </ul>
                </div>
            </article>
        );
    }
}

ProductReferenceComponent.propTypes = {
    currentReference:                PropTypes.shape({
        id:                      PropTypes.number.isRequired,
        label:                   PropTypes.string.isRequired,
        mainImage:               PropTypes.shape({
            urlThumbnail: PropTypes.string.isRequired,
            altThumbnail: PropTypes.string.isRequired,
        }),
        unitPriceIncludingTaxes: PropTypes.number.isRequired
    }),
    currency:                        PropTypes.string.isRequired,
    activatedSliderIndexByReference: PropTypes.object.isRequired,
};

const mapStateToProps = (state) => {
    return {
        activatedSliderIndexByReference: state.product.activatedSliderIndexByReference,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {};
};

export default connect(mapStateToProps, mapDispatchToProps)(ProductReferenceComponent);
