import './app/_navbar'
import './app/_search-form'
import './app/_navbar'

const script = document.body.dataset.script
if (script !== '') {
  console.log(script)
  import(`./app/${script}`)
}
