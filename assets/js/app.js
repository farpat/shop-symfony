import "react-hot-loader/patch";
import 'bootstrap';
import './app/_search-form';

const script = document.body.dataset.script;
if (script !== '') {
    import (`./app/${script}`);
}
