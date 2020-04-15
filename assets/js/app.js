import 'bootstrap';
import './app/search-form';

const script = document.body.dataset.script;

if (script !== '') {
    import (`./app/${script}`);
}
