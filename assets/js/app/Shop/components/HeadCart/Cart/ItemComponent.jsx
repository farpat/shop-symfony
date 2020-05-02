import React from "react"
import PropTypes from 'prop-types'

class ItemComponent extends React.Component {
    constructor(props) {
        super(props)
    }

    goToReference(url) {
        alert('Goto: ' + url)
    }

    render() {
        return (
            <tr className="header-cart-item">
                <td>
                    <input type="number" value={this.props.item.quantity}/>
                </td>
                <td>
                    <a href={this.props.item.reference.url}
                       onClick={this.goToReference.bind(this.props.item.reference.url)}>
                        {this.props.item.reference.product.label} | {this.props.item.reference.label}
                    </a>
                </td>
                <td>
                    Amount including taxes
                </td>
                <td className="header-cart-item-td-icon">
                    <button className="btn btn-sm btn-link p-0" type="button">
                        <i className="fas fa-times"></i>
                    </button>
                </td>
            </tr>

        )
    }
}

ItemComponent.propTypes = {
    item: PropTypes.object.isRequired
}

export default ItemComponent
