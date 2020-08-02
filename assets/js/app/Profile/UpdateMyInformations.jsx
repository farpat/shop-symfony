import React, { useEffect, useRef, useState, useCallback } from 'react'
import TextComponent from '../ui/Form/TextComponent'
import EmailComponent from '../ui/Form/EmailComponent'
import { jsonGet, jsonPut } from '@farpat/api'
import { getError } from '../ui/Form/Form'
import NotBlankRule from '../../src/Security/Rules/NotBlankRule'
import EmailRule from '../../src/Security/Rules/EmailRule'
import Arr from '../../src/Arr'
import Alert from '../ui/Alert/Alert'

const rules = {
  name: [new NotBlankRule({ message: 'This value should not be blank' })],
  email: [new NotBlankRule({ message: 'This value should not be blank' }), new EmailRule({ message: 'This value is not a valid email address' })]
}

const navLinkProfile = document.querySelector('#nav-link-profile')

function UpdateMyInformations() {
  const [state, setState] = useState({
    information: {},
    errors: {},
    alert: null,
    isLoading: true,
    isSubmitting: false
  })

  const form = useRef(null)

  useEffect(() => {
    (async function () {
      const response = await jsonGet('/profile-api/me')
      setState({
        ...state,
        information: response,
        isLoading: false
      })
    })()
  }, [])

  useEffect(() => {
    if (!state.isLoading && !state.isSubmitting && Arr.isEmpty(state.errors)) {
      navLinkProfile.innerText = state.information.name
    }
  }, [state.isSubmitting])

  function onSubmit(event) {
    event.preventDefault()

    if (state.isSubmitting) {
      return false
    }

    setState({ ...state, isSubmitting: true, alert: null })

    jsonPut('/profile-api/me', state.information)
      .then(response => {
        setState({
          ...state,
          errors: {},
          alert: { type: 'success', message: 'Information updated with success!' },
          information: response,
          isSubmitting: false
        })
      })
      .catch(errors => {
        setState({
          ...state,
          errors,
          alert: { type: 'danger', message: 'Information not updated' },
          isSubmitting: false,
        })
      })
  }

  const onUpdateInformation = (key, value) => {
    const error = getError(rules, key, value)

    setState({
      ...state,
      information: { ...state.information, [key]: value },
      errors: { ...state.errors, [key]: error }
    })
  }

  if (state.isLoading) {
    return <div className="text-center mt-5">
      <i className='fas fa-spinner spinner fa-7x' />
    </div>
  }

  return <form ref={form} className='mb-5' onSubmit={onSubmit}>
    {
      state.alert &&
      <Alert type={state.alert.type} message={state.alert.message}
        onClose={() => setState({ ...state, alert: null })} />
    }
    
    <TextComponent id="name" name="name" label="Name" isRequired={true}
      onUpdate={onUpdateInformation} attr={{ autoFocus: true }}
      error={state.errors.name} value={state.information.name}
    />

    <EmailComponent id="email" name="email" label="Email" isRequired={true}
      onUpdate={onUpdateInformation}
      error={state.errors.email} value={state.information.email}
    />

    {
      state.isSubmitting ?
        <button className="btn btn-primary" disabled><i className="fa fa-spinner spinner" /> Loading&hellip;
            </button> :
        <button className="btn btn-primary">Save informations</button>
    }
  </form>
}

export default UpdateMyInformations
