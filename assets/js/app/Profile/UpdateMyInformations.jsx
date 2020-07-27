import React, { useEffect, useRef, useState, useCallback } from 'react'
import Str from '../../src/Str'
import TextComponent from '../Form/TextComponent'
import EmailComponent from '../Form/EmailComponent'
import Rule from '../../src/Security/Rule'
import { jsonGet, jsonPut } from '@farpat/api'
import { getError } from '../Form/Form'
import NotBlankRule from '../../src/Security/Rules/NotBlankRule'
import EmailRule from '../../src/Security/Rules/EmailRule'

function UpdateMyInformations () {
  const [state, setState] = useState({
    information      : {},
    staticInformation: {},
    errors           : {},
    alert            : null,
    isLoading        : true,
    submitIsLoading  : false
  })

  const form = useRef(null)

  useEffect(() => {
    (async function () {
      const response = await jsonGet('/profile-api/me')
      setState({
        ...state,
        information      : response,
        staticInformation: response,
        isLoading        : false
      })
    })()
  }, [])

  function handleSubmit (event) {
    event.preventDefault()

    setState({ ...state, submitIsLoading: true, alert: null })

    jsonPut('/profile-api/me', state.information)
      .then(response => setState({
        ...state,
        errors           : {},
        alert            : { type: 'success', message: 'Information updated with success!' },
        information      : response,
        staticInformation: response,
        submitIsLoading  : false
      }))
      .catch(errors => setState({
        ...state,
        errors,
        alert          : { type: 'danger', message: 'Information not updated' },
        submitIsLoading: false,
      }))
  }

  const onUpdateInformation = (key, value) => {
    const error = getError({
      name : [new NotBlankRule({ message: 'This value should not be blank' })],
      email: [new NotBlankRule({ message: 'This value should not be blank' }), new EmailRule({ message: 'This value is not a valid email address' })]
    }, key, value)

    setState({
      ...state,
      information: { ...state.information, [key]: value },
      errors     : { ...state.errors, [key]: error }
    })
  }

  return <>
    {
      state.isLoading ?
        <div className="text-center">
          <i className='fas fa-spinner spinner fa-7x'/>
        </div> :
        <form ref={form} className='mb-5' onSubmit={handleSubmit}>
          {
            !state.isLoading && <>
              {
                state.alert &&
                <div className={`alert alert-${state.alert.type} alert-dismissible fade show`} role="alert">
                  {state.alert.message}
                  <button type="button" className="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              }
              <TextComponent id="name" name="name" label="Name" isRequired={true}
                             onUpdate={onUpdateInformation} attr={{ autoFocus: true }}
                             error={state.errors.name} value={state.staticInformation.name}
              />

              <EmailComponent id="email" name="email" label="Email" isRequired={true}
                              onUpdate={onUpdateInformation}
                              error={state.errors.email} value={state.staticInformation.email}
              />

              {
                state.submitIsLoading ?
                  <button className="btn btn-primary" disabled><i className="fa fa-spinner spinner"/> Loading&hellip;
                  </button> :
                  <button className="btn btn-primary">Save informations</button>
              }
            </>
          }
        </form>
    }
  </>
}

export default UpdateMyInformations
