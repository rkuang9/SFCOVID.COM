class OrionForm {
    getValue(id) {
        return document.getElementById(id).value;
    }

    setValue(id, value) {
        document.getElementById(id).value = value;
    }

    setVisible(id, boolean) {
        document.getElementById(id).display = boolean;
    }

    setMandatory(id, boolean) {
        document.getElementById(id).required = boolean;
    }

    setReadOnly(id, boolean) {
        document.getElementById(id).readOnly = boolean;
    }

    clearValue(id) {
        document.getElementById(id).value = '';
    }

    setStyle(id) {
        return document.getElementById(id).style;
    }
}

let form = new OrionForm();