import React from "react";
import {connect} from "react-redux";
import PropTypes from "prop-types";
import Str from "../../../../src/String/Str";

class ProductComponent extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div className="col-md-4 mb-3">
                <article className="card product">
                    {
                        this.props.product.image &&
                        <a href={this.props.product.url}>
                            <img src={this.props.product.image.url_thumbnail}
                                 alt={this.props.product.image.alt_thumbnail}
                                 className="card-img-top"/>
                        </a>
                    }
                    <div className="card-body">
                        <h3 className="card-title"><a href={this.props.product.url}>{this.props.product.label}</a></h3>
                        <div className="card-text">
                            {this.props.product.excerpt}

                            <p className="mt-2 m-0">
                                From <span className="badge badge-secondary">{Str.toLocaleCurrency(this.props.product.min_unit_price_including_taxes, this.props.currency)}</span>
                            </p>
                        </div>
                    </div>
                </article>
            </div>
        );
    }
}

ProductComponent.propTypes = {
    product:  PropTypes.shape({
        id:                             PropTypes.number.isRequired,
        url:                            PropTypes.string.isRequired,
        excerpt:                        PropTypes.string,
        label:                          PropTypes.string.isRequired,
        min_unit_price_including_taxes: PropTypes.number.isRequired,
        image:                          PropTypes.shape({
            url_thumbnail: PropTypes.string.isRequired,
            alt_thumbnail: PropTypes.string,
        })
    }),
    currency: PropTypes.string.isRequired
};

const mapStateToProps = (state) => {
    return {
        currency: state.products.currency
    }
};

const mapDispatchToProps = (dispatch) => {
    return {}
};

export default connect(mapStateToProps, mapDispatchToProps)(ProductComponent);
