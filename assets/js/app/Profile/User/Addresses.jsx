import React, { useEffect, useRef, useState } from 'react'
import { jsonGet, jsonPut } from '@farpat/api'
import TextComponent from '../../ui/Form/TextComponent'
import places from 'places.js'
import Alert from '../../ui/Alert/Alert'

const getName = function (index, key) {
  return `addresses[${index}][${key}]`
}

const getError = function (errors, index) {
  return errors.addresses ? errors.addresses[index] : undefined
}

const addAddress = function (addresses, newAddress) {
  return [...addresses, newAddress]
}

const deleteAddress = function (addresses, indexToDelete) {
  addresses[indexToDelete] = {
    id    : addresses[indexToDelete].id,
    status: 'DELETED',
  }

  return addresses
}

const updateAddress = function (addresses, indexToUpdate, addressToUpdate) {
  addresses[indexToUpdate] = {
    ...addresses[indexToUpdate],
    ...addressToUpdate,
    status: addressToUpdate.id ? 'UPDATED' : 'ADDED'
  }

  return addresses
}

function Addresses () {
  const form = useRef(null)
  const [state, setState] = useState({
    information : {},
    errors      : {},
    alert       : null,
    isLoading   : true,
    isSubmitting: false
  })

  useEffect(() => {
    (async function () {
      const response = await jsonGet('/api/profile/user/addresses')
      setState({
        ...state,
        information: response,
        isLoading  : false
      })
    })()
  }, [])

  const onUpdateSecondLine = (key, value) => {
    const [, formattedKey] = key.match(/^addresses\[([0-9]+)]/)

    const addresses = state.information.addresses
    addresses[window.parseInt(formattedKey)]['line2'] = value

    setState({
      ...state,
      information: { ...state.information, addresses },
      errors     : { ...state.errors }
    })
  }

  const onAddAddress = () => {
    let deliveryAddressIndex = state.information.delivery_address_index ?? state.information.addresses.length

    const addresses = addAddress(state.information.addresses, {
      id          : null,
      text        : '',
      line1       : '',
      line2       : '',
      postal_code : '',
      city        : '',
      country     : '',
      country_code: '',
      status      : 'ADDED'
    })

    setState({
      ...state,
      information: {
        ...state.information,
        addresses,
        delivery_address_index: deliveryAddressIndex
      }
    })
  }

  const onDeleteAddress = (index) => {
    let deliveryAddressIndex = state.information.delivery_address_index
    if (index === state.information.delivery_address_index) {
      deliveryAddressIndex = state.information.addresses.findIndex((address, indexToFind) => indexToFind !== index && address.status !== 'DELETED')

      if (deliveryAddressIndex === -1) {
        deliveryAddressIndex = null
      }
    }

    setState({
      ...state,
      information: {
        ...state.information,
        addresses             : deleteAddress(state.information.addresses, index),
        delivery_address_index: deliveryAddressIndex
      }
    })
  }

  const onSelectAddress = (index) => {
    setState({
      ...state,
      information: {
        ...state.information,
        delivery_address_index: index
      }
    })
  }

  const onSubmit = function (event) {
    event.preventDefault()

    if (state.isSubmitting) {
      return false
    }

    setState({ ...state, isSubmitting: true, alert: null })

    jsonPut('/api/profile/user/addresses', state.information)
      .then(response => {
        setState({
          ...state,
          errors      : {},
          alert       : { type: 'success', message: 'Addresses updated with success!' },
          information : response,
          isSubmitting: false
        })
      })
      .catch(errors => {
        setState({
          ...state,
          errors,
          alert       : { type: 'danger', message: 'Information not updated' },
          isSubmitting: false,
        })
      })
  }

  useEffect(() => {
    if (state.isLoading === false) {
      form.current.querySelectorAll('input.algolia').forEach((input, index) => {
        if (input.classList.contains('ap-input')) {
          return
        }

        const placesAutoComplete = places({
          appId    : state.information.algolia.id,
          apiKey   : state.information.algolia.key,
          container: input
        })
          .configure({ language: 'en', type: 'address' })

        placesAutoComplete.setVal(state.information.addresses[index].text)

        placesAutoComplete.on('change', event => {
          setState({
            ...state,
            information: {
              ...state.information,
              addresses: updateAddress(state.information.addresses, index, {
                ...state.information.addresses[index],
                text        : event.suggestion.value,
                line1       : event.suggestion.name,
                postal_code : event.suggestion.postcode,
                city        : event.suggestion.city,
                country     : event.suggestion.country,
                country_code: event.suggestion.countryCode.toUpperCase(),
              })
            }
          })
        })

        placesAutoComplete.on('clear', () => {
          setState({
            ...state,
            information: {
              ...state.information,
              addresses: updateAddress(state.information.addresses, index, {
                id          : null,
                text        : '',
                line1       : '',
                line2       : '',
                postal_code : '',
                city        : '',
                country     : '',
                country_code: '',
              })
            }
          })
        })
      })
    }
  }, [state.information.addresses])

  if (state.isLoading) {
    return <div className="text-center mt-5">
      <i className='fas fa-spinner spinner fa-7x'/>
    </div>
  }

  return <form ref={form} className='mb-5' onSubmit={onSubmit}>
    <h1>Update my addresses</h1>
    {
      state.alert &&
      <Alert type={state.alert.type} message={state.alert.message}
             onClose={() => setState({ ...state, alert: null })}/>
    }

    <div className="addresses">
      {
        state.information.addresses.map((address, index) => {
          if (address.status && address.status === 'DELETED') {
            return null
          }

          return <Address address={address} index={index} key={index}
                          onUpdateSecondLine={onUpdateSecondLine} onDeleteAddress={onDeleteAddress}
                          onSelectAddress={onSelectAddress}
                          isSelected={state.information.delivery_address_index === index}
                          error={getError(state.errors, index)}
          />
        })
      }
      <button type="button" className="btn btn-link text-success address address-add" onClick={onAddAddress}>
        <i className="fa fa-plus-circle"/> Add address
      </button>
    </div>

    {
      state.isSubmitting ?
        <button className="btn btn-primary" disabled><i className="fa fa-spinner spinner"/> Loading&hellip;
        </button> :
        <button className="btn btn-primary">Save information</button>
    }
  </form>
}

export default Addresses

function Address ({ address, index, error, isSelected, onUpdateSecondLine, onDeleteAddress, onSelectAddress }) {
  return <div className={`address ${isSelected ? 'selected' : ''}`}>

    <TextComponent id={'address_text_' + index} className='algolia'
                   name={getName(index, 'text')} error={error} value={address.text}/>

    <TextComponent id={'address_line2_' + index} name={getName(index, 'line2')} value={address.line2}
                   attr={{ placeholder: 'Address line 2' }}
                   onUpdate={onUpdateSecondLine}/>

    <div>
      <button type="button" className="btn btn-link text-danger" onClick={() => onDeleteAddress(index)}>Delete</button>
      {
        !isSelected &&
        <button type="button" className="btn btn-link text-primary" onClick={() => onSelectAddress(index)}>Define by
          default</button>
      }
    </div>
  </div>
}
