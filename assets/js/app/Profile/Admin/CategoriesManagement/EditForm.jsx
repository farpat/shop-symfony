import TextComponent from '../../../ui/Form/TextComponent'
import React, { createRef, useState } from 'react'
import FileComponent from '../../../ui/Form/FileComponent'
import { jsonPut } from '@farpat/api'
import CheckboxComponent from '../../../ui/Form/CheckboxComponent'
import ChoiceComponent from '../../../ui/Form/ChoiceComponent'

function EditForm ({ categoryItem, cancelEditingOrAdding, setCategoryItems }) {
  const [prefix, ...suffix] = categoryItem.category.nomenclature.split('.')
  const startNomenclature = suffix.length > 0 ? prefix : ''
  const endNomenclature = suffix.length > 0 ? suffix.join('.') : prefix

  const [state, setState] = useState({
    errors      : {},
    category    : {
      id            : categoryItem.category.id,
      nomenclature  : categoryItem.category.nomenclature,
      endNomenclature,
      label         : categoryItem.category.label,
      description   : categoryItem.category.description,
      image         : categoryItem.category.image,
      product_fields: categoryItem.category.product_fields
    },
    isSubmitting: false
  })

  const onUpdate = function (name, value) {
    if (name === 'end-nomenclature') {
      name = 'nomenclature'
      value = (startNomenclature !== '' ? startNomenclature + '.' : '') + value.replaceAll('.', '')
    } else {
      const regex = /product_fields\[(\w+)\]\[(\d+)\]/
      const results = name.match(regex)
      if (results) {
        let [, field, index] = results
        index = parseInt(index, 10)
        name = 'product_fields'
        value = state.category.product_fields.map((product_field, i) => {
          return i === index ?
            { ...product_field, status: 'UPDATED', [field]: value } :
            product_field
        })
      }
    }

    console.log(name, value)

    setState({ ...state, category: { ...state.category, [name]: value } })
  }

  const onSubmit = async () => {
    debugger

    const formData = new FormData(this)

    if (state.isSubmitting) {
      return
    }

    try {
      setState({ ...state, isSubmitting: true })
      const categories = await jsonPut(`/api/profile/admin/categories/${state.category.id}/edit`, formData)
      setCategoryItems(categories)
      setState({ ...state, isSubmitting: false })
    } catch (error) {
      console.error(error)
    }
  }

  return <form onSubmit={(event) => {
    event.preventDefault()
    onSubmit()
  }}>
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
    {
      categoryItem.category.is_last_level &&
      <ProductFieldsLegend productFields={state.category.product_fields} onUpdate={onUpdate}/>
    }
    <FooterButtons isSubmitting={state.isSubmitting} cancelEditingOrAdding={cancelEditingOrAdding}/>
  </form>
}

function ProductFieldsLegend ({ productFields, onUpdate }) {
  return <table className="table product-fields-table">
    <thead>
    <tr>
      <th>Type</th>
      <th>Label</th>
      <th style={{ width: '8rem' }}>Is required ?</th>
      <th></th>
    </tr>
    </thead>
    <tbody>
    {
      productFields.map((productField, index) => {
        if (productField.status && productField.status === 'DELETED') {
          return null
        }

        return <tr key={index} style={{ verticalAlign: 'middle' }}>
          <td>
            <ChoiceComponent name={`product_fields[type][${index}]`} id={`product_fields[type][${index}]`}
                             choices={[{ value: 'string', label: 'String' }, { value: 'number', label: 'Numeric' }]}
                             wrapperClassName={'m-0'} onUpdate={onUpdate}/>
          </td>
          <td>
            <TextComponent id={`product_fields[label][${index}]`} name={`product_fields[label][${index}]`}
                           value={productField.label} wrapperClassName="m-0" onUpdate={onUpdate}/>
          </td>
          <td>
            <CheckboxComponent id={`product_fields[is_required][${index}]`}
                               name={`product_fields[is_required][${index}]`}
                               wrapperClassName="m-0 d-flex justify-content-center" value={productField.is_required}
                               onUpdate={onUpdate}/>
          </td>
          <td>
            <button type="button" className="btn btn-link text-danger"
                    onClick={() => onUpdate(`product_fields[status][${index}]`, 'DELETED')}>&times;</button>
          </td>
        </tr>
      })
    }
    </tbody>
  </table>
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
