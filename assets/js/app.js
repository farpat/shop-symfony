import 'bootstrap';
import 'popper.js';
import ReactDOM from "react-dom";
import Example from "./src/components/Example";
import React from "react";

const script = document.body.dataset.script;

if (script !== '') {
    import (`./app/${script}`);
}


// const exampleElement = document.getElementById('example');
// ReactDOM.render(<Example/>, exampleElement);