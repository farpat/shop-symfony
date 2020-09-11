import TextComponent from '../../../ui/Form/TextComponent'

function AddForm ({ categoryItem, cancelEditingOrAdding, setCategoryItems }) {
  const onSubmit = async (event) => {
    event.preventDefault()
    try {
      const categories = await jsonPost(`/api/profile/admin/categories/new`, item)
      setCategoryItems(categories)
    } catch (error) {
      console.error(error)
    }
  }

  return <form onSubmit={onSubmit}>
    <div className="row align-items-center g-1 m-0 mb-3">
      <label className="col-auto m-0" htmlFor={'nomenclature'}>  {categoryItem.category.nomenclature}.</label>
      <div className="col m-0">
        <TextComponent wrapperclassName={'m-0'} id={'nomenclature'} name={'nomenclature'}
                       attr={{ placeholder: 'Nomenclature' }}/>
      </div>
    </div>

    <TextComponent id={'label'} label="Label" name={'label'} attr={{ placeholder: 'Label' }}/>

    <TextComponent id={'description'} label="Description" name={'description'}
                   attr={{ placeholder: 'Description' }}/>

    <button className="btn btn-link" onClick={cancelEditingOrAdding}>Cancel</button>
    <button className="btn btn-success">Add</button>
  </form>
}

export default AddForm
