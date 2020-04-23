import React from "react";
import PropTypes from 'prop-types';
import {connect} from "react-redux";

class ReferenceNavItemComponent extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div className={this.getLiClass()}>
                <a className="nav-product-reference-item-container"
                   onClick={this.setCurrentReference.bind(this, this.props.reference)}>
                    {
                        this.props.reference.mainImage &&
                        <img src={this.props.reference.mainImage.urlThumbnail}
                             alt={this.props.reference.mainImage.altThumbnail}/>
                    }
                    <h2 className={this.getTitleClass()}>{this.props.reference.label}</h2>
                </a>
            </div>
        );
    }

    getLiClass() {
        let className = 'nav-product-reference-item';
        if (this.props.reference === this.props.currentReference) {
            className += ' bg-primary';
        }
        return className;
    }

    getTitleClass() {
        let className = 'nav-product-reference-item-title';
        if (this.props.reference === this.props.currentReference) {
            className += ' text-white';
        }
        return className;
    }

    setCurrentReference(reference, event) {
        event.preventDefault();
        this.props.setCurrentReference(reference);
    }
}

ReferenceNavItemComponent.propTypes = {
    reference:        PropTypes.shape({
        id:        PropTypes.number.isRequired,
        label:     PropTypes.string.isRequired,
        mainImage: PropTypes.shape({
            urlThumbnail: PropTypes.string.isRequired,
            altThumbnail: PropTypes.string.isRequired,
        })
    }),
    currentReference: PropTypes.shape({
        id:    PropTypes.number.isRequired,
        label: PropTypes.string.isRequired,
    }),

    setCurrentReference: PropTypes.func.isRequired,
};

const mapStateToProps = (state) => {
    return {
        currentReference: state.product.currentReference
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        setCurrentReference: (reference) => dispatch({type: 'UPDATE_REFERENCE', reference}),
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(ReferenceNavItemComponent);
