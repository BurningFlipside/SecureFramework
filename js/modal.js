const defaultOptions = {
  message: null,
  title: null,
  // Use the returned promise instead of bootbox's callback
  show: true,
  closeButton: true,
  buttons: {},
  animate: true,
  size: null,
};

class PlainBSModal {
  constructor() {
    this.modalDiv = document.createElement('div');
    this.modalDiv.setAttribute('class', 'modal');
    this.modalDiv.setAttribute('tabindex', '-1');
    this.modalDiv.setAttribute('role', 'dialog');
    this.dialogDiv = document.createElement('div');
    this.dialogDiv.setAttribute('class', 'modal-dialog');
    this.contentDiv = document.createElement('div');
    this.contentDiv.setAttribute('class', 'modal-content');
    this.headerDiv = document.createElement('div');
    this.headerDiv.setAttribute('class', 'modal-header');
    this.contentDiv.appendChild(this.headerDiv);
    this.bodyDiv = document.createElement('div');
    this.bodyDiv.setAttribute('class', 'modal-body');
    this.contentDiv.appendChild(this.bodyDiv);
    this.footerDiv = document.createElement('div');
    this.footerDiv.setAttribute('class', 'modal-footer');
    this.contentDiv.appendChild(this.footerDiv);
    this.dialogDiv.appendChild(this.contentDiv);
    let {promise, resolve, reject} = Promise.withResolvers();
    this.promise = promise;
    this.resolve = resolve;
    this.reject = reject;
    this.modalDiv.appendChild(this.dialogDiv);
    document.body.appendChild(this.modalDiv);
    this.bsModal = new bootstrap.Modal(this.modalDiv);
  }
  setTitle(title) {
    if(title) {
      if(typeof title === 'string') {
        this.headerDiv.innerText = title;
      } else {
        this.headerDiv.appendChild(title);
      }
    }
  }
  setMessage(message) {
    if(message) {
      if(typeof message === 'string') {
        this.bodyDiv.innerText = message;
      } else {
        this.bodyDiv.appendChild(message);
      }
    }
  }
  addCloseButton(closeButton) {
    if(closeButton) {
      closeButton = document.createElement('button');
      closeButton.setAttribute('type', 'button');
      closeButton.setAttribute('class', 'btn-close');
      closeButton.setAttribute('data-bs-dismiss', 'modal');
      closeButton.setAttribute('aria-label', 'Close');
      this.headerDiv.appendChild(closeButton);
    }
  }
  addButtons(buttons) {
    for(let [name, obj] of Object.entries(buttons)) {
      let button = document.createElement('button');
      button.setAttribute('type', 'button');
      button.setAttribute('class', `btn ${obj.className}`);
      button.innerText = obj.label;
      this.footerDiv.appendChild(button);
      if(obj.callback) {
        button.addEventListener('click', obj.callback);
      } else {
        button.addEventListener('click', () => {
          this.defaultCallback(name);
        });
      }
    }
  }
  defaultCallback(name) {
    let values = {};
    let inputs = this.bodyDiv.querySelectorAll('input, textarea, select');
    for(let input of inputs) {
      if(input.type === 'checkbox') {
        values[input.name] = input.checked;
        continue;
      }
      if(input.type === 'radio') {
        if(input.checked) {
          values[input.name] = input.value;
        }
        continue;
      }
      values[input.name] = input.value;
    }
    let retObj = {name: name, modal: this};
    switch(Object.keys(values).length) {
      case 0:
        break;
      case 1:
        retObj.value = values[Object.keys(values)[0]];
        break;
      default:
        retObj.values = values;
        break;
    }
    if(name === 'cancel') {
      this.reject(retObj);
    } else {
      this.resolve(retObj);
    }
    this.hide();
    this.remove();
  }
  show() {
    this.bsModal.show();
  }
  hide() {
    this.bsModal.hide();
  }
  remove() {
    this.modalDiv.remove();
  }
  createInput(type, options) {
    let input = document.createElement('input');
    input.setAttribute('type', type);
    input.setAttribute('class', 'form-control');
    if(options.placeholder) {
      input.setAttribute('placeholder', options.placeholder);
    }
    if(options.value) {
      input.setAttribute('value', options.value);
    }
    if(options.min) {
      input.setAttribute('min', options.min);
    }
    if(options.max) {
      input.setAttribute('max', options.max);
    }
    return input;
  }
  createCheckboxes(options) {
    let container = document.createElement('div');
    if(!options.inputOptions || options.inputOptions.length === 0) {
      throw new Error('No inputOptions provided for checkboxes');
    }
    for(let option of options.inputOptions) {
      let div = document.createElement('div');
      div.setAttribute('class', 'form-check');
      let input = document.createElement('input');
      input.setAttribute('type', 'checkbox');
      input.setAttribute('class', 'form-check-input');
      input.setAttribute('name', option.value);
      div.appendChild(input);
      let label = document.createElement('label');
      label.setAttribute('class', 'form-check-label');
      label.setAttribute('for', option.value);
      label.innerText = option.text;
      div.appendChild(label);
      container.appendChild(div);
    }
    return container;
  }
  createRadios(options) {
    let container = document.createElement('div');
    if(!options.inputOptions || options.inputOptions.length === 0) {
      throw new Error('No inputOptions provided for radios');
    }
    for(let option of options.inputOptions) {
      let div = document.createElement('div');
      div.setAttribute('class', 'form-check');
      let input = document.createElement('input');
      input.setAttribute('type', 'radio');
      input.setAttribute('class', 'form-check-input');
      input.setAttribute('name', options.name);
      input.setAttribute('value', option.value);
      div.appendChild(input);
      let label = document.createElement('label');
      label.setAttribute('class', 'form-check-label');
      label.setAttribute('for', option.value);
      label.innerText = option.text;
      div.appendChild(label);
      container.appendChild(div);
    }
    return container;
  }
  createSelect(options) {
    let select = document.createElement('select');
    select.setAttribute('class', 'form-select');
    select.setAttribute('name', options.name);
    if(options.inputOptions) {
      for(let option of options.inputOptions) {
        let optionElement = document.createElement('option');
        optionElement.setAttribute('value', option.value);
        optionElement.innerText = option.text;
        select.appendChild(optionElement);
      }
    }
    return select;
  }
  static dialog(options) {
    let myOptions = Object.assign({}, defaultOptions, options);
    let m = new PlainBSModal();
    m.setTitle(myOptions.title);
    m.addCloseButton(myOptions.closeButton);
    m.setMessage(myOptions.message);
    m.addButtons(myOptions.buttons);
    if(myOptions.show) {
      m.show();
    }
    return m.promise;
  }
  static alert(message) {
    return PlainBSModal.dialog({
      message: message,
      buttons: {
        ok: {
          label: 'OK',
          className: 'btn-primary',
        },
      },
    });
  }
  static confirm(message) {
    return PlainBSModal.dialog({
      message: message,
      buttons: {
        ok: {
          label: 'OK',
          className: 'btn-primary',
        },
        cancel: {
          label: 'Cancel',
          className: 'btn-secondary',
        },
      },
    });
  }
  static prompt(options) {
    let myOptions = Object.assign({}, defaultOptions, {buttons: {
      ok: {
        label: 'OK',
        className: 'btn-primary',
      },
      cancel: {
        label: 'Cancel',
        className: 'btn-secondary',
      },
    },}, options);
    let m = new PlainBSModal();
    m.setTitle(myOptions.title);
    m.addCloseButton(myOptions.closeButton);
    switch(myOptions.inputType) {
      case 'text':
        // Fallthrough... same option...
      default:
        myOptions.message = m.createInput('text', myOptions);
        break;
      case 'password':
        myOptions.message = m.createInput('password', myOptions);
        break;
      case 'email':
        myOptions.message = m.createInput('email', myOptions);
        break;
      case 'date':
        myOptions.message = m.createInput('date', myOptions);
        break;
      case 'time':
        myOptions.message = m.createInput('time', myOptions);
        break;
      case 'number':
        myOptions.message = m.createInput('number', myOptions);
        break;
      case 'range':
        myOptions.message = m.createInput('range', myOptions);
        break;
      case 'textarea':
        myOptions.message = document.createElement('textarea');
        myOptions.message.setAttribute('class', 'form-control');
        break;
      case 'checkbox':
        myOptions.message = m.createCheckboxes(myOptions);
        break;
      case 'radio':
        myOptions.message = m.createRadios(myOptions);
        break;
      case 'select':
        myOptions.message = m.createSelect(myOptions);
        break;
    }
    m.setMessage(myOptions.message);
    m.addButtons(myOptions.buttons);
    if(myOptions.show) {
      m.show();
    }
    return m.promise;
  }
}

const isModule = typeof module !== 'undefined' && module.exports;
if(isModule) {
  module.exports = PlainBSModal;
}