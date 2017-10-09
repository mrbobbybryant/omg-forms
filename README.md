# OMG Forms

A WordPress Forms Solution built specifically for Developers.

## Table of Contents

- [Background](#why)
- [Install](#installation)
- [Usage](#usage)
- [API](#api)
- [Lsit of Addons](addons)
- [Roadmap](#roadmap)

## Why
As a developer I have often found working with the various Forms Plugins in WordPress to be a pain in the butt. The primary pain point is that these forms generate a ton of deeply nest markup which makes styling these forms to match a design very painful.

Plus sometimes the design calls for radical changes in how that markup is built. While all the top forms plugins allow you the ability to change that markup, it is not usually very straight-forward.

It is my assumption that these plugins have this issue because they were built for the end user, not Developers. You see, most form plugins have a drag and drop interface that allows non-programmers the ability to create the forms. Complex form even! And that is awesome.

But this really highlights the fact that on day one, these plugins were not built for developers. They were built for power users.

### Introducing OMG Forms
That is where OMG Forms comes into play. It was built from day one to be for developers and developers only. But how can that be? Don't you need to make it for Power Users? Nope.
I believe:
  - 90% of user only need one or two forms.
  - Once a site goes live users will rarely need to change these forms much, or at all.
  - Even if a user did want a new form, odds are I would be tasked with helping them, even though they technically have the ability to make the form themselves.

If that is the case, then why wouldn't I use a forms solution that puts the developer experience first? That is where OMG Forms comes into play.

## Installation
OMG Forms can be installed via composer.
```sh
$ composer require developwithwp/omg-forms
```

Once you have installed this package you will need to call Composer's autoloader if your project is not already.
```php
if ( file_exists( get_template_directory() . '/vendor/autoload.php' ) ) {
    require( 'vendor/autoload.php' );
}
```

## Usage
You are now ready to create your first form. OMG Forms comes with a helper method for creating new forms `\OMGForms\Core\register_form()`.

This function expects an array of arguments similar to how `register_post_type` expects an array of arguments.

To start lets define a very simple form.

```php
$args = [
	'name'              =>  'contact-form',
	'redirect'          =>  false,
	'email'             =>  false,
	'form_type'         =>  'basic-form',
	'success_message'   =>  'Thank you!',
	'fields' => [
		[
			'slug'      =>   'your-name',
			'label'     =>   'Your Name',
			'type'      =>   'text',
			'required'  =>   false
		],
		[
			'slug'      =>  'email-address',
			'label'     =>  'Your Email',
			'type'      =>  'email',
			'required'  =>   true
		],
		[
			'slug'      =>   'company',
			'label'     =>   'Your Company',
			'type'      =>   'text',
			'required'  =>   false
		]
	]
];
\OMGForms\Core\register_form( $args );
```

As you can see the form allows for a lot of configuration at both the form and the field level.

Once you have defined a form, you can render it by calling `display_form`.
```php
echo \OMGForms\Core\display_form( 'my-form-name' );
```

Or via a built-in shortcode.
```
[omgform form="my-form-name"]
```
## API

### Form Settings

> The follow api settings will dictate how your new form will function at a global level.

**name:** - Should be a *unique name* for this form. i.e. `contact-form` or `contactForm`.

**redirect** - Allows you the ability to redirect the form after a successful submission. In order for this to work, you must always provide a `redirect_url` argument as well.

**redirect_url** - A valid URL to redirect the user to after a successful form submission.

**email** - Allows you the ability to notify someone via email whenever a form is submitted.

**email_to** - A valid email address of the person to notify after a successful form submission. *(NOTE: Currentl yonly supports notifying one person)*

**success_message** - Allows you the ability to customize the success message a user is shown. *This is only used when `redirect` to set to false.*

**form_type** - OMG Forms is built using an addon model. This setting lets you specify which addon or type of form you want this form to be. i.e `basic-form` or `mailchimp` etc...

**classname** - Allows you do add a custom class to the form wrapper.

**fields** - An array of all the field types, and their properties.

### Field Settings

> Each form field has a number of settings which you can use to dictate how that form will look and act.

**slug** - A computer readable unique name for the field.

**label** - A Human readable name for this field. By default each field needs a label. This is an important accessibility best practice.

**type** - Lets you specify what type of HTML5 field this should be.
Supports
 - text
 - email
 - checkbox
 - multicheckbox
 - number
 - password
 - radio
 - select
 - tel - *(telephone)*
 - textarea

**required** - Can be `true` or `false`. Allows you the ability to make a field required.

**placeholder** - Lets you set a placeholder value.

**error** - Lets you define an error message for this field if it fails server side validation.

**template** - While OMG Forms has a built way to override a fields html markup across the board. This settings lets you set a template on a per form or per field basis. *Note: template name cannot match any of the default field template names)*

**Example**
```php
[
    'slug'      => 'first-name',
    'label'     =>  'First Name',
    'type'      =>  'text',
    'required'  =>  true,
    'template'  =>  'text-larger.php'
]
```

### Customizing the Form
OMG Forms has a built in mechanism which makes override the field html a breeze.
**Step 1** Create a directory in the root of your theme called `forms`.
**Step 2** Create a file for the field you wish to overrider. *(NOTE: Make sure you do the same name.)* So if you want to override how the text fields are looking them you need to create a field called `text.php`.
**Step 3** Write your own markup and **profit**. To get started I would encourage you to copy the existing markup and use that as a starting point.

## Roadmap
  - Add Logic for conditional fields
