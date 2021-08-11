class OrionAjax {

    /**
     * Open a new XHR object
     *
     * @param site,     API route provided at object declaration
     * @param method,   GET or POST
     */
    constructor(site, method) {
        this.ajax = new XMLHttpRequest();
        this.site = site;
        this.get_parameters = '';
        this.post_parameters = '';
        this.method = method;
    }

    /**
     * Pass in parameters for the GET request
     *
     * @param parameter,    GET parameter
     * @param value,        parameter value
     */
    addParam = function(parameter, value) {
        if (this.get_parameters === '') {
            this.get_parameters += '?' + parameter + '=' + value;
        }
        else {
            this.get_parameters += '&' + parameter + '=' + value;
        }
    }



    addPostParam = function(parameter, value) {
        if (this.post_parameters === '') {
            this.post_parameters += parameter + '=' + value;
        }
        else {
            this.post_parameters += '&' + parameter + '=' + value;
        }
    }


    /**
     * Perform ajax call to specified using provided address
     * When the operation is DONE (4) and status is OK (200), run the provided callback function
     *
     * @param callback,     function callback
     * @param callback2,    optional: second function callback
     * @return JSON.parse(this.ajax.responseText) as json to the callback function
     */
    getResponse = function(callback, callback2 = null) {
        if (this.method.toLowerCase() === 'post') {
            this.ajax.open(this.method, this.site, true);
            this.ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            this.ajax.send(this.post_parameters);
        }
        else if (this.method.toLowerCase() === 'get') {
            this.ajax.open(this.method, this.site + this.get_parameters, true);
            this.ajax.send();
        }

        this.ajax.onreadystatechange = (function(ajax) {
            return function() {
                if (ajax.readyState === XMLHttpRequest.DONE && ajax.status === 200) {
                    if (typeof callback == "function") {
                        callback(ajax.responseText);
                    }
                    if (typeof callback2 == "function") {
                        callback2(ajax.responseText);
                    }
                }
            }
        })(this.ajax);
    }
}
