import TextComponent from '../../../ui/Form/TextComponent'
import React, { createRef, useRef, useState } from 'react'
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
    isSubmitting: false
  })

  const onSubmit = async (form) => {
    const formData = new FormData(form)

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
    onSubmit(event.target)
  }}>
    <div className="row">
      <div className="col">

        <CategoryLegend category={categoryItem.category}
                        endNomenclature={endNomenclature}
                        startNomenclature={startNomenclature}
                        isDisplayNomenclatureLabel={suffix.length > 0}/>
      </div>
      <div className="col">
        <ImageLegend image={categoryItem.category.image}/>
      </div>
    </div>
    {
      categoryItem.category.is_last_level &&
      <ProductFieldsLegend productFields={categoryItem.category.product_fields}/>
    }
    <FooterButtons isSubmitting={state.isSubmitting} cancelEditingOrAdding={cancelEditingOrAdding}/>
  </form>
}

function ProductFieldsLegend ({ productFields }) {
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
                             wrapperClassName={'m-0'}/>
          </td>
          <td>
            <TextComponent id={`product_fields[label][${index}]`} name={`product_fields[label][${index}]`}
                           value={productField.label} wrapperClassName="m-0"/>
          </td>
          <td>
            <CheckboxComponent id={`product_fields[is_required][${index}]`}
                               name={`product_fields[is_required][${index}]`}
                               wrapperClassName="m-0 d-flex justify-content-center" value={productField.is_required}/>
          </td>
          <td>
            <button type="button" className="btn btn-link text-danger">&times;</button>
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

function ImageLegend ({ image }) {
  const isDeletedRef = useRef(null)

  return <fieldset>
    <legend>Image</legend>

    <FileComponent id={`image[url]`} name={`image[url]`}
                   onUpdate={(name, value) => {
                     isDeletedRef.current.value = 0
                   }}
                   onDelete={(name) => {
                     isDeletedRef.current.value = 1
                   }}
                   currentText={image.label}
                   initialText={'Choose an image'}
                   buttonText={'Browse'}/>

    <input type="hidden" name={`image[is_deleted]`} defaultValue="0" ref={isDeletedRef}/>

    <TextComponent id={`image[alt]`} label="Image description" name={`image[alt]`}
                   attr={{ placeholder: '(alt)' }}
                   value={image ? image.alt : ''}
    />
  </fieldset>
}

function CategoryLegend ({ category, endNomenclature, startNomenclature, isDisplayNomenclatureLabel }) {
  const labelClassName = isDisplayNomenclatureLabel ? '' : ' d-none'
  const endNomenclatureRef = createRef()

  return <fieldset>
    <legend>Category</legend>

    <div className="row align-items-center g-1 m-0 mb-3">
      <label className={'col-auto m-0' + labelClassName} htmlFor={'end-nomenclature'}>{startNomenclature}.</label>
      <div className="col m-0">
        <TextComponent wrapperClassName={'m-0'} id={'end-nomenclature'} name={'end-nomenclature'}
                       ref={endNomenclatureRef}
                       value={endNomenclature} onUpdate={(name, value) => {
          value = value.replaceAll('.', '')
          endNomenclatureRef.current.value = value
        }} attr={{ placeholder: 'Nomenclature' }}
        />

        <input type="hidden" name="nomenclature" value={category.nomenclature}/>
      </div>
    </div>

    <TextComponent id={'label'} label="Label" name={'label'} attr={{ placeholder: 'Label' }}
                   value={category.label}/>

    <TextComponent id={'description'} label="Description" name={'description'} attr={{ placeholder: 'Description' }}
                   value={category.description}/>
  </fieldset>
}

export default EditForm
