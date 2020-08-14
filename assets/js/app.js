import './app/_navbar'
import './app/_search-form'
import './app/_navbar'

const script = document.body.dataset.script
if (script !== '') {
  console.log(`--- LOADING ./app/${script}.js ---`)
  import(`./app/${script}`)
}
