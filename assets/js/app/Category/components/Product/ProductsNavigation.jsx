import React from "react";
import PropTypes from 'prop-types';
import {connect} from "react-redux";
import range from "lodash/range";

class ProductsNavigation extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        const pages = this.getPages();

        return (
            <nav aria-label="Product pagination" className={this.getNavClass(pages)}>
                <ul className="pagination">
                    <li className={this.getPreviousItemClass()}>
                        <a href="#" onClick={this.goTo.bind(this, this.props.currentPage - 1)}
                           className="page-link">&larr; Previous</a>
                    </li>
                    {
                        pages.map(page =>
                            <li className={this.getItemClass(page)} key={page}>
                                <a href="#" onClick={this.goTo.bind(this, page)}
                                   className="page-link">{page}</a>
                            </li>)
                    }
                    <li className={this.getNextItemClass()}>
                        <a href="#" onClick={this.goTo.bind(this, this.props.currentPage + 1)}
                           className="page-link">Next &rarr;</a>
                    </li>
                </ul>
            </nav>
        );
    }

    getNavClass(pages) {
        if (pages.length === 0) {
            return 'd-none';
        }

        return 'mt-2';
    }

    getPages() {
        const pagesCount = Math.ceil(this.props.products.length / this.props.perPage);
        if (pagesCount === 0) {
            return [];
        }
        return range(1, pagesCount + 1);
    }

    getItemClass(page) {
        let className = 'page-item';
        if (this.props.currentPage === page) {
            className += ' active';
        }

        return className;
    }

    getPreviousItemClass() {
        let className = 'page-item';
        if (this.props.currentPage === 1) {
            className += ' disabled';
        }

        return className;
    }

    getNextItemClass() {
        let className = 'page-item';
        if (this.props.currentPage === this.getPages().length) {
            className += ' disabled';
        }

        return className;
    }

    goTo(pageToSet, event) {
        event.preventDefault();
        this.props.goTo(pageToSet);
    }
}

ProductsNavigation.propTypes = {
    products:    PropTypes.arrayOf(PropTypes.shape({
        id:      PropTypes.number.isRequired,
        url:     PropTypes.string.isRequired,
        excerpt: PropTypes.string,
        label:   PropTypes.string.isRequired,
        image:   PropTypes.shape({
            urlThumbnail: PropTypes.string.isRequired
        })
    })),
    perPage:     PropTypes.number.isRequired,
    currentPage: PropTypes.number.isRequired,

    goTo: PropTypes.func.isRequired,
};

const mapStateToProps = (state) => {
    return {
        products:    state.products.currentProducts,
        perPage:     state.products.perPage,
        currentPage: state.products.currentPage
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        goTo: (page) => dispatch({type: 'UPDATE_PAGE', page})
    }
};

export default connect(mapStateToProps, mapDispatchToProps)(ProductsNavigation);
