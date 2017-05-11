# Bubbles :monkey_face:

## Usage

### Set up

You'll need to run

```
bower install
```

from plugin directory to install Axios.

In Settings > Bubbles, you'll need to add these two params:

* `mailchimp_api_key` - Secret key for MailChimp account
* `mailchimp_datacenter` - Datacenter associated with mailchimp account (e.g. 'us15')

### Shortcode

Shortcode usage:

```
[mailchimp_signup]
```

which accepts the following attributes:

* `form_class` (default: `'form-inline'`) - Class for <form>
* `input_class` (default: `'form-control'`) - Class for email <input>
* `list_id` (default: `''`) - ID of MailChimp list *required*
* `placeholder` (default: `'Enter email address'`) - Placeholder text for email <input>
* `submit_class` (default: `'btn'`) - Class for submit <input>
* `submit_text` (default: `'Sign up'`) - Value for submit <input>


## Build

```
npm install
```

to install build tools.

```
gulp scripts
```

to run build tasks.

## TODO

* Include Axios in a way that doesn't require `bower install`.
