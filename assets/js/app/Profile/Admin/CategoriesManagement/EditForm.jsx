import TextComponent from '../../../ui/Form/TextComponent'
import React, { createRef, useState } from 'react'
import FileComponent from '../../../ui/Form/FileComponent'
import { jsonPut } from '@farpat/api'
import PropTypes from 'prop-types'

function EditForm ({ categoryItem, cancelEditingOrAdding, setCategoryItems }) {
  const [prefix, ...suffix] = categoryItem.category.nomenclature.split('.')
  const startNomenclature = suffix.length > 0 ? prefix : ''
  const endNomenclature = suffix.length > 0 ? suffix.join('.') : prefix

  const [state, setState] = useState({
    errors      : {},
    category    : {
      id          : categoryItem.category.id,
      nomenclature: categoryItem.category.nomenclature,
      endNomenclature,
      label       : categoryItem.category.label,
      description : categoryItem.category.description,
    },
    isSubmitting: false
  })

  const onUpdate = function (name, value) {
    if (name === 'end-nomenclature') {
      name = 'nomenclature'
      value = (startNomenclature !== '' ? startNomenclature + '.' : '') + value.replaceAll('.', '')
    }

    setState({ ...state, category: { ...state.category, [name]: value } })
  }

  const onSubmit = async (event) => {
    event.preventDefault()
    const formData = new FormData(event.target)

    debugger
    return false

    if (state.isSubmitting) {
      return
    }

    try {
      event.preventDefault()
      setState({ ...state, isSubmitting: true })
      const categories = await jsonPut(`/api/profile/admin/categories/${state.category.id}/edit`, formData)
      setCategoryItems(categories)
      setState({ ...state, isSubmitting: false })
    } catch (error) {
      console.error(error)
    }
  }

  return <form onSubmit={onSubmit}>
    <div className="row">
      <div className="col">

        <CategoryLegend category={categoryItem.category} state={state} onUpdate={onUpdate}
                        startNomenclature={startNomenclature}
                        isDisplayNomenclatureLabel={suffix.length > 0}/>
      </div>
      <div className="col">
        <ImageLegend category={categoryItem.category}/>
      </div>
    </div>


    <FooterButtons isSubmitting={state.isSubmitting} cancelEditingOrAdding={cancelEditingOrAdding}/>
  </form>
}

function FooterButtons ({ isSubmitting, cancelEditingOrAdding }) {
  return <>
    <button className="btn btn-link" type="button" onClick={cancelEditingOrAdding}>Cancel</button>
    {
      isSubmitting ?
        <button className="btn btn-primary" disabled={true} type="submit">Editing...</button> :
        <button className="btn btn-primary" type="submit">Edit</button>
    }
  </>
}

function ImageLegend ({ category }) {
  const imageRef = createRef()

  return <fieldset>
    <legend>Image</legend>

    <FileComponent id={'url'} name={'url'} ref={imageRef} onDelete={() => imageRef.current.value = ''}
                   initialText={category.image ? category.image.label : 'Browse'}
                   onUpdate={(name, files) => {
                     console.log(name, files)
                   }}/>

    <TextComponent id={'alt'} label="Image description" name={'alt'} attr={{ placeholder: '(alt)' }}
                   value={category.image.alt}
                   onUpdate={(name, value) => console.log(name, value)}/>
  </fieldset>
}

function CategoryLegend ({ category, state, onUpdate, startNomenclature, isDisplayNomenclatureLabel }) {
  const endNomenclatureRef = createRef()
  const labelClassName = isDisplayNomenclatureLabel ? '' : ' d-none'

  return <fieldset>
    <legend>Category</legend>

    <div className="row align-items-center g-1 m-0 mb-3">
      <label className={'col-auto m-0' + labelClassName} htmlFor={'end-nomenclature'}>{startNomenclature}.</label>
      <div className="col m-0">
        <TextComponent wrapperClassName={'m-0'} id={'end-nomenclature'} name={'end-nomenclature'}
                       ref={endNomenclatureRef}
                       value={state.category.endNomenclature} onUpdate={onUpdate}
                       attr={{ placeholder: 'Nomenclature' }}
        />

        <input type="hidden" name="nomenclature" value={state.category.nomenclature}/>
      </div>
    </div>

    <TextComponent id={'label'} label="Label" name={'label'} attr={{ placeholder: 'Label' }}
                   value={category.label} onUpdate={onUpdate}/>

    <TextComponent id={'description'} label="Description" name={'description'} attr={{ placeholder: 'Description' }}
                   value={category.description} onUpdate={onUpdate}/>
  </fieldset>
}

export default EditForm
