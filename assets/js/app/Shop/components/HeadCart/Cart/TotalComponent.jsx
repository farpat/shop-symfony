import React from "react"
import PropTypes from 'prop-types'

class TotalComponent extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        return (
            <tfoot className="header-cart-total">
            <tr>
                <td colspan="2">Total price:</td>
                <td colspan="2">Total price in €</td>
            </tr>
            <tr className="header-cart-total-vat">
                <td className="text-right" colspan="2"> Including taxes:</td>
                <td colspan="2">Formatted including taxes in €</td>
            </tr>
            <tr>
                <td colspan="4">
                    <a className="float-right btn btn-primary" href="/purchase">
                        Purchase
                    </a>
                </td>
            </tr>
            </tfoot>

        )
    }
}

TotalComponent.propTypes = {
    items: PropTypes.object.isRequired
}

export default TotalComponent
