# OMG Forms

A WordPress Forms Solution built specifically for Developers.

## Table of Contents

- [Background](#why)
- [Install](#installation)
- [Usage](#usage)
- [API](#api)
- [Field Types](#supported-field-types)
- [Customizing the Form](#customizing-the-form)
- [List of Hooks](#list-of-hooks)
- [List of Addons](addons)
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

### Goals
OMG Forms has a few core goals that it is focused on achieving.
 - Make it easy for developer to override the default field templates to match their design.
 - Provide a Developer API which will make it easy for developers to define a form and it fields, without the need to click around in the WordPress Admin. A secondary goal of this feature is to make it a fast and repeatable process for creating forms.
 - Uses an Addon model so that Developers can extend the core OMG Forms Library to make their own Addons.
 - OMG Forms will eventually be able to replace all of your form related needs on the frontend. i.e. Login, Register, Payments, Frontend Post Creation, Contact Forms, Subscription Forms, etc. This will allow developers a way to have consisting markup and styles for all of their sites forms site-wide.

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

### \OMGForms\Core\register_form([args])

#### args

##### name

Type: `string` *(unique name)*
Default: `none`

Argument is used to give your form a unique name. This is the name you will use when it comes time to display your form. Form names need to be `snake_case` or `camelCase`.

##### form_type

Type: `string/array` *(basic-form)*
Default: `none`

This setting tells OMG Forms which Addon, or 'type" of form you are making. For example if this were a standard contact form, then you would be using the Basic addon and this argument would be set to `basic-form`. Other examples would be `authorize_net` and `constant-contact`.\

This setting could could also be an array of form_types. This is useful when you need a form to serve multiple purposes. For example `[ 'authorize_net', 'basic-form' ]`. This configuration will cause your form to charge a user via Authorize.net while also saving a copy of the transaction as a Basic Form Submission.

##### redirect

Type: `boolean` *(true/false)*
Default: `none`

Used to tell OMG Forms that it should redirect after a successful form submission.

##### redirect_to

Type: `string` *(url)*
Default: `none`

If `redirect` is set to `true`, OMG Forms will redirect the user to this page.

##### email

Type: `boolean` *(true/false)*
Default: `none`

Used to tell OMG Forms if it should notify someone via email after a successful form submission.

##### email_to

Type: `string` *(email address)*
Default: `none`

If `email` is set to `true`, OMG Forms will email this person once a form has been successfully submitted.

##### success_message

Type: `boolean` *(true/false)*
Default: `none`

This message will replace the form after a successful form submission. Unless of course you have set the form to **redirect**. In that case, the form will simply redirect.

##### rest_api

Type: `boolean` *(true/false)*
Default: `false`

This setting is used by Addon Authors to tell OMG Forms if their addon should use the PHP Api or the Javascript Api. `false = JS API` and `true = PHP API`

Again, End Users should not worry about this option. It is up to the addon developer to handle setting this option automatically. Addon author should *see the [hooks list](#list-of-hooks) for information about how to do this properly*.

##### classname

Type: `string`
Default: `none`

This argument lets you define a css class name which will be added to the form wrapper HTML Element.

##### groups

Type: `array`
Default: `none`

This argument lets you define groups. Groups are used to "group" various form fields together. Each group in the groups array is an array itself, which contains the following arguments:
- **id** - `string` *(snake_case)* - Should be unique for each form.
- **title** - `string` - Allows you the ability to give each section, or group of the form a title.
- **order** - `number` - This is used by OMG Forms to know what order to place this group in the form.
- **class** - `string` *(optional)* - Adds a CSS class to the group wrapper HTML Element.

##### Groups Example
```php
'groups'    => [
            [
                'id'        => 'group_1',
                'title'     => esc_html__('Group One Title', 'text-domain'),
                'order'     => '1',
                'class'     => 'my-group-class'
            ],
            [
                'id'        => 'group_2',
                'title'     => esc_html__('Group Two Title', 'text-domain'),
                'order'     => '2'
            ]
        ]
```

##### fields

Type: `array`
Default: `none`

The fields argument will contain all of the fields your form will need. This is where all the magic happens. Each field in the fields array is an array itself, which may contain the following arguments:
- **slug** - `string` *(snake_case)* *(required)* - Should be unique for each form.
- **label** - `string` *(required)* - This text will be used for the HTML Label associated to this form input.
- **type** - `string` *(required)* - Tells OMF Forms what type of HTML form field this field should use. See the supported Fields Lists to see all the field types supported by OMG Forms.
- **required** - `bool` *(true/false)* - Tells OMG Forms if this is a required field. If set to true, then a **required** attribute will be added to the HTML input field. If you choose to omit this from your own form templates then OMG Forms will check that these fields are not empty server side.
- **class** - `string` *(CSS Class)* - Will add a CSS class to the wrapper form element.
- **placeholder** - `string` - Will add a placeholder to the HTML output by OMG Forms.
- **template** - `string` *(file_name)* - One of two ways to tell OMG Forms to load your own template for this field type. *See [Modify OMG Form field Templates](#customizing-the-form), for more information.*
- **group** - `string` - If your form is using Groups, then OMG Forms will use this argument to properly group form fields when displaying a form.
- **options** - `array` - If this field is a type which has multiple options, then this argument is used to list those options. Each option in the options array is an associated array, and should have a `value` and a `label` key.
- **sanitize_cb** - `string` - Argument is used server side to sanitize the field's value. By default OMG Forms sanitizes all field values, but this argument give you more granular control.

##### Fields Example

```php
'fields' => [
    [
        'slug'          =>  'my-slug',
        'type'          =>  'select',
        'label'         =>  esc_html__( 'My Select', 'text-domain'),
        'class'         =>  'my-select-class',
        'template'      =>  'my-select.php',
        'placeholder'   =>  esc_html__( 'Select Placeholder', 'text-domain' ),
        'options'       =>  [
            [
                'value' =>  'value_1',
                'label' =>  esc_html__( 'Value One', 'text-domain' )
            ],
            [
                'value' =>  'value_2',
                'label' =>  esc_html__( 'Value Two', 'text-domain' )
            ]
        ],
        'group'         => 'group_1'
    ],
    [
        'slug'          =>  'my-number',
        'type'          =>  'number',
        'label'         =>  esc_html__( 'My Label', 'text-domain'),
        'class'         =>  'my-class',
        'template'      =>  'my-template.php',
        'group'         =>  'group_1',
        'sanitize_cb'   =>  'absint'
    ]
]
```

## Supported Field Types
- checkbox
- email
- hidden
- multi-checkbox
- number
- password
- radio
- select
- submit
- telephone
- text
- textarea

## Customizing the Form

> OMG Forms has a built in mechanism which makes override the field html a breeze.

Their are two ways that you can tell OMG Forms which templates to load when rendering a certain field type. However regardless of which option you choose the first thing you need to do is create a `forms` directory in the root of your theme.

**Option 1** This is the easiest option.
 - Create a file for the field type you wish to overrider, and place it in the `forms` folder. *(NOTE: Make sure you use the same name.)* So if you want to override the templates for all the text fields, then create a field called `text.php`.
 - As a starter we recommend that you copy the code from the template that ships with OMG Forms. This will give you a good base to start with as you modify the field's HTML markup.

**Option 2** This option is useful if you need to change the markup for a field type on a per form/field basis. This options has two steps:
 - Create a file and place it in the `forms` folder. You will want to give this file a unique name. For example if we are changing the text field, we don't want to name the file `text.php`. We would name it something like `donate-text.php`.
 - Once the file is created and you have wrote the HTML Markup for this new text field, you need to tell the form to use this field template. You can do this by passing the file name to the `template` argument when you register the field in the form. *See the [fields API](#fields) section for more info.

## List of Hooks

#### Action Hooks

`omg_form_before_form` - Allows developers a way to add HTML Markup **before** the form HTML.

`omg_form_before_form_submit` - Allows developers a way to add HTML Markup *after the HTML form fields, but before* the form submit button.

`omg_form_after_form` - Allows developers a way to add HTML Markup **after** the form HTML.

`omg_form_validation` - Provides a way to add additional validation checks. These checks are run by OMG Forms on page load, and are used to notify developers about possible issues they may have with their form.

`omg-form-settings-hook` - OMG Forms comes with a settings page. This hook allows Addon Authors a way to add additional settings sections and fields to this page.

#### Filter Hooks

`omg_forms_save_data` - *This is the primary hook used by addon authors to creat their addon*, This hook exposes the sanitized and validated data so that Addon can use this data to do whatever it is that addon is supposed to do.

`rest_allow_anonymous_entries` Allows Addon Authors to restrict form submissions to logged in users. By default this is set to true, meaning anonymous user **can** submit forms.

`omg_forms_sanitize_data` - Allows Addon Authors to set additional sanitization steps to the sanitization process.

`omg-form-filter-field-args` - Allows Addon Authors a way to modify field arguments after they have been set when the form was registered. This is useful when you need to ensure a certain form is setup with a specific field argument.

`omg_form_filter_register_args` - Similar to `omg-form-filter-field-args`, this filter lets you modify the entire form arguments after a form was created. For example, lots of Addons use this filter to force a certain form of a specific type to set `rest_api => true`.

## Roadmap
  - Add Logic for conditional fields
