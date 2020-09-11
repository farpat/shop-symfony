import TextComponent from '../../../ui/Form/TextComponent'
import Dump from '../../../ui/Dump'

function EditForm ({ categoryItem, cancelEditingOrAdding, setCategoryItems }) {
  const [prefix, ...suffix] = categoryItem.category.nomenclature.split('.')
  const labelClassName = suffix.length > 0 ? '' : ' d-none'
  const startNomenclature = suffix.length > 0 ? prefix : ''
  const endNomenclature = suffix.length > 0 ? suffix.join('.') : prefix
  const endNomenclatureRef = createRef()

  const [state, setState] = useState({
    category    : {
      id          : categoryItem.category.id,
      nomenclature: categoryItem.category.nomenclature,
      label       : categoryItem.category.label,
      description : categoryItem.category.description,
    },
    isSubmitting: false
  })

  const onUpdate = function (name, value, startNomenclature) {
    if (name === 'nomenclature') {
      value = (startNomenclature !== '' ? startNomenclature + '.' : '') + value.replaceAll('.', '')
    }

    setState({ ...state, category: { ...state.category, [name]: value } })
  }

  const onSubmit = async (event) => {
    if (state.isSubmitting) {
      return
    }

    try {
      event.preventDefault()
      setState({ ...state, isSubmitting: true })
      const categories = await jsonPut(`/api/profile/admin/categories/${state.category.id}/edit`, state.category)
      setCategoryItems(categories)
      setState({ ...state, isSubmitting: false })
    } catch (error) {
      console.error(error)
    }
  }

  return <form onSubmit={onSubmit}>
    <div className="row align-items-center g-1 m-0 mb-3">
      <label className={'col-auto m-0' + labelClassName} htmlFor={'nomenclature'}>{startNomenclature}.</label>
      <div className="col m-0">
        <TextComponent wrapperclassName={'m-0'} id={'nomenclature'} name={'nomenclature'} ref={endNomenclatureRef}
                       value={endNomenclature} onUpdate={(name, value) => onUpdate(name, value, startNomenclature)}
                       attr={{ placeholder: 'Nomenclature' }}
        />
      </div>
    </div>

    <Dump object={categoryItem.category}/>

    <TextComponent id={'label'} label="Label" name={'label'} attr={{ placeholder: 'Label' }}
                   value={categoryItem.category.label} onUpdate={onUpdate}/>

    <TextComponent id={'description'} label="Description" name={'description'} attr={{ placeholder: 'Description' }}
                   value={categoryItem.category.description} onUpdate={onUpdate}/>

    <button className="btn btn-link" type="button" onClick={cancelEditingOrAdding}>Cancel</button>

    {
      state.isSubmitting ?
        <button className="btn btn-primary" disabled={true} type="submit">Editing...</button> :
        <button className="btn btn-primary" type="submit">Edit</button>
    }
  </form>
}

export default EditForm
