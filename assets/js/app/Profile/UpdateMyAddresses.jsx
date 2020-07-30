import React, { useRef, useState, useEffect } from 'react'
import { jsonGet } from '@farpat/api'
import Str from '../../src/Str'
import TextComponent from '../ui/Form/TextComponent'
import places from 'places.js'

const getName = function (index, key) {
  return `addresses[${index}][${key}]`
}

function UpdateMyAddresses () {
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
      const response = await jsonGet('/profile-api/addresses')
      setState({
        ...state,
        information: response,
        isLoading  : false
      })
    })()
  }, [])

  useEffect(() => {
    if (state.isLoading === false) {
      form.current.querySelectorAll('input.algolia').forEach((input, index) => {
        const placesAutoComplete = places({
          appId    : state.information.algolia.id,
          apiKey   : state.information.algolia.key,
          container: input
        })
          .configure({ language: 'en', type: 'address' })

        placesAutoComplete.setVal(state.information.addresses[index].text)

        placesAutoComplete.on('change', event => {
          const addresses = state.information.addresses

          addresses[index] = {
            ...addresses[index],
            id          : addresses[index].id,
            text        : event.suggestion.value,
            line1       : event.suggestion.name,
            latitude    : event.suggestion.latlng.lat,
            longitude   : event.suggestion.latlng.lng,
            postal_code : event.suggestion.postcode,
            city        : event.suggestion.city,
            country     : event.suggestion.country,
            country_code: event.suggestion.countryCode.toUpperCase(),
            is_deleted  : false,
          }

          setState({
            ...state,
            information: { ...state.information, addresses }
          })
        })

        placesAutoComplete.on('clear', () => {
          const addresses = state.information.addresses

          addresses[index] = {
            id        : addresses[index].id,
            is_deleted: true,
          }

          setState({
            ...state,
            information: { ...state.information, addresses }
          })
        })
      })
    }
  }, [state.isLoading])

  const onUpdateLine2 = (key, value) => {
    const [, formattedKey] = key.match(/^addresses\[([0-9]+)\]/)

    const addresses = state.information.addresses
    addresses[window.parseInt(formattedKey)]['line2'] = value

    setState({
      ...state,
      information: { ...state.information, addresses },
      errors     : { ...state.errors }
    })
  }

  const addAddress = () => {
    const addresses = state.information.addresses
    addresses.push({
      id          : null,
      text        : '',
      line1       : '',
      line2       : '',
      postal_code : '',
      city        : '',
      country     : '',
      country_code: '',
      latitude    : null,
      longitude   : null
    })

    setState({
      ...state,
      information: { ...state.information, addresses }
    })
  }

  if (state.isLoading) {
    return <div className="text-center mt-5">
      <i className='fas fa-spinner spinner fa-7x'/>
    </div>
  }

  return <form ref={form} className='mb-5'>
    <div dangerouslySetInnerHTML={{ __html: Str.dump(state) }}></div>

    <button className="btn btn-link text-success" onClick={addAddress}>Add address</button>

    <div className="addresses">
      {
        state.information.addresses.map((address, index) => {
          if (address.is_deleted === true) {
            return <>
              <input type="hidden" name={getName(index, 'id')} value={address.id}/>
              <input type="hidden" name={getName(index, 'is_deleted')} value="true"/>
            </>
          }

          return <Address address={address} index={index} key={index} onUpdateLine2={onUpdateLine2}
                          error={state.errors.addresses ? state.errors.addresses[index] : undefined}></Address>
        })
      }
    </div>


    {
      state.isSubmitting ?
        <button className="btn btn-primary" disabled><i className="fa fa-spinner spinner"/> Loading&hellip;
        </button> :
        <button className="btn btn-primary">Save informations</button>
    }
  </form>
}

export default UpdateMyAddresses

function Address ({ address, index, error, onUpdateLine2 }) {
  return <div className='address'>
    <h2>{`Address ${index}`}</h2>

    <TextComponent id={'address_text_' + index} className={'algolia'} name={getName(index, 'text')} isRequired={true}
                   attr={{ autoFocus: index === 0 }}
                   error={error} value={address.text}
    />

    <TextComponent id={'address_line2_' + index} name={getName(index, 'line2')} value={address.line2}
                   attr={{ placeholder: 'Line 2' }}
                   onUpdate={onUpdateLine2}/>
  </div>
}

