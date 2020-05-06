import React from "react"
import PropTypes from 'prop-types'
import Str from "../../../../../src/String/Str"
import Translation from "../../../../../src/Translation/Translation"

class TotalComponent extends React.Component {
    constructor(props) {
        super(props)
    }

    getPrices() {
        let totalPriceExcludingTaxes = 0
        let totalPriceIncludingTaxes = 0

        Object.keys(this.props.items).map(referenceId => {
            const item = this.props.items[referenceId]
            totalPriceExcludingTaxes += item.quantity * item.reference.unitPriceExcludingTaxes
            totalPriceIncludingTaxes += item.quantity * item.reference.unitPriceIncludingTaxes
        })

        return {
            totalPriceExcludingTaxes,
            totalPriceIncludingTaxes,
            totalIncludingTaxes: totalPriceIncludingTaxes - totalPriceExcludingTaxes
        }
    }

    render() {
        const {totalPriceExcludingTaxes, totalIncludingTaxes} = this.getPrices()

        return (
            <tfoot className="header-cart-total">
            <tr>
                <td colSpan="2">{Translation.get('Total price without taxes')}:</td>
                <td colSpan="2">{Str.toLocaleCurrency(totalPriceExcludingTaxes, this.props.currency)}</td>
            </tr>
            <tr className="header-cart-total-vat">
                <td className="text-right" colSpan="2">{Translation.get('Including taxes')}:</td>
                <td colSpan="2">{Str.toLocaleCurrency(totalIncludingTaxes, this.props.currency)}</td>
            </tr>
            <tr>
                <td colSpan="4">
                    <a className="float-right btn btn-primary" href="/purchase">{Translation.get('Purchase')}</a>
                </td>
            </tr>
            </tfoot>

        )
    }
}

TotalComponent.propTypes = {
    items:       PropTypes.objectOf(PropTypes.shape({
        quantity:  PropTypes.number.isRequired,
        reference: PropTypes.shape({
            unitPriceIncludingTaxes: PropTypes.number.isRequired,
            unitPriceExcludingTaxes: PropTypes.number.isRequired
        })
    })).isRequired,
    purchaseUrl: PropTypes.string.isRequired,
    currency:    PropTypes.string.isRequired
}

export default TotalComponent
