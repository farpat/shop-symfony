import React from "react";
import PropTypes from "prop-types";

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
                        <a href="#"><img src={this.props.product.image.url_thumbnail}
                                         alt={this.props.product.image.alt_thumbnail}
                                         className="card-img-top"/></a>
                    }
                    <div className="card-body">
                        <h3 className="card-title"><a href={this.props.product.url}>{this.props.product.label}</a></h3>
                        <p className="card-text">{this.props.product.excerpt}</p>
                    </div>
                </article>
            </div>
        );
    }
}

ProductComponent.propTypes = {
    product: PropTypes.shape({
        id:      PropTypes.number.isRequired,
        url:     PropTypes.string.isRequired,
        excerpt: PropTypes.string,
        label:   PropTypes.string.isRequired,
        image:   PropTypes.shape({
            url_thumbnail: PropTypes.string.isRequired
        })
    })
};

export default ProductComponent;
