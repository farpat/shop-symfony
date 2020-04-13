import 'bootstrap';

const script = document.body.dataset.script;

if (script !== '') {
    import (`./app/${script}`);
}
