import React from "react";
import PropTypes from 'prop-types';
import {range} from "lodash";

class ProductsNavigation extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <nav aria-label="Product pagination" className="mt-2">
                <ul className="pagination">
                    <li className={this.getPreviousItemClass()}>
                        <a href="#" onClick={this.goTo.bind(this, this.props.currentPage - 1)}
                           className="page-link">&larr; Previous</a>
                    </li>
                    {
                        this.getPages().map(page => {
                            return <li className={this.getItemClass(page)} key={page}>
                                <a href="#" onClick={this.goTo.bind(this, page)} className="page-link">{page}</a>
                            </li>
                        })
                    }
                    <li className={this.getNextItemClass()}>
                        <a href="#" onClick={this.goTo.bind(this, this.props.currentPage + 1)}
                           className="page-link">Next &rarr;</a>
                    </li>
                </ul>
            </nav>
        );
    }

    getPages() {
        const pagesCount = Math.ceil(this.props.products.length / this.props.perPage);
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
    }
}

ProductsNavigation.propTypes = {
    products:    PropTypes.arrayOf(PropTypes.shape({
        id:      PropTypes.number.isRequired,
        url:     PropTypes.string.isRequired,
        excerpt: PropTypes.string,
        label:   PropTypes.string.isRequired,
        image:   PropTypes.shape({
            url_thumbnail: PropTypes.string.isRequired
        })
    })),
    perPage:     PropTypes.number.isRequired,
    currentPage: PropTypes.number.isRequired
};

export default ProductsNavigation;