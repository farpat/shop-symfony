import 'bootstrap';
import 'popper.js';

const script = document.body.dataset.script;

if (script !== '') {
    import (`./app/${script}`);
}
