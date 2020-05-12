import React from 'react'
import PropTypes from 'prop-types'
import Str from '../../../../../src/String/Str'
import { connect } from 'react-redux'
import Requestor from '@farpat/api'

class ItemComponent extends React.Component {
  constructor (props) {
    super(props)
  }

  goToReference (url, event) {
    event.preventDefault()
    const referenceUrlObject = new URL(window.location.origin + url)
    const currentUrlObject = new URL(window.location.href)

    if (referenceUrlObject.pathname === currentUrlObject.pathname) { // force the url loading if only hash is different
      window.location.href = referenceUrlObject.origin + referenceUrlObject.pathname + '?r=1' + referenceUrlObject.hash
    } else {
      window.location.href = url
    }
  }

  changeQuantity (event) {
    this.props.updateItemQuantity(this.props.item.reference, Number.parseInt(event.target.value))
  }

  deleteItem () {
    this.props.deleteItem(this.props.item.reference)
  }

  isLoading () {
    const isLoading = this.props.isLoading[this.props.item.reference.id]

    return isLoading === undefined ? false : isLoading
  }

  render () {
    return (
      <tr className='header-cart-item'>
        <td>
          <input
            type='number' className='cart-item-quantity' value={this.props.item.quantity}
            onChange={this.changeQuantity.bind(this)}
          />
        </td>
        <td className='header-cart-item-td-label'>
          {
            this.props.item.reference.mainImage &&
              <img
                src={this.props.item.reference.mainImage.urlThumbnail}
                alt={this.props.item.reference.mainImage.altThumbnail} width={72} className='mr-2'
              />
          }
          <a
            href={this.props.item.reference.url}
            onClick={this.goToReference.bind(this, this.props.item.reference.url)}
          >
            {this.props.item.reference.label}
          </a>
        </td>
        <td>
          {Str.toLocaleCurrency(this.props.item.reference.unitPriceIncludingTaxes, this.props.currency)}
        </td>
        <td className='header-cart-item-td-icon'>
          {
            !this.isLoading() &&
              <button className='btn btn-sm p-0' type='button' onClick={this.deleteItem.bind(this)}>
                <i className='fas fa-times' />
              </button>
          }
          {
            this.isLoading() &&
              <span><i className='fas fa-spinner spinner' /></span>
          }
        </td>
      </tr>

    )
  }
}

ItemComponent.propTypes = {
  item: PropTypes.shape({
    quantity: PropTypes.number.isRequired,

    reference: PropTypes.shape({
      url: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      unitPriceIncludingTaxes: PropTypes.number.isRequired,
      unitPriceExcludingTaxes: PropTypes.number.isRequired,
      mainImage: PropTypes.shape({
        urlThumbnail: PropTypes.string.isRequired,
        altThumbnail: PropTypes.string.isRequired
      })
    })
  }),
  currency: PropTypes.string.isRequired,

  updateItemQuantity: PropTypes.func.isRequired
}

const mapStateToProps = (state) => {
  return {
    isLoading: state.cart.itemInLoading
  }
}
const mapStateToDispatch = (dispatch) => {
  return {
    updateItemQuantity: (reference, quantity) => {
      dispatch({ type: 'IS_LOADING', reference, isLoading: true })

      Requestor.newRequest()
        .patch(`/cart-items/${reference.id}`, {
          quantity
        })
        .then(response => {
          dispatch({ type: 'UPDATE_ITEM_QUANTITY', reference: response.reference, quantity })
          dispatch({ type: 'IS_LOADING', reference, isLoading: false })
        })
        .catch(error => console.error(error))
    },
    deleteItem: (reference) => {
      dispatch({ type: 'IS_LOADING', reference, isLoading: true })

      Requestor.newRequest()
        .delete(`/cart-items/${reference.id}`)
        .then(() => {
          dispatch({ type: 'DELETE_ITEM', reference })
          dispatch({ type: 'IS_LOADING', reference, isLoading: false })
        })
        .catch(error => console.error(error))
    }
  }
}

export default connect(mapStateToProps, mapStateToDispatch)(ItemComponent)
