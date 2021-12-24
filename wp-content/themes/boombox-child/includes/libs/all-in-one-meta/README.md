# All In One Meta ( AIOM )

All In One Meta is a **WordPress** library that allows developers easily create meta boxes with user friendly interface and store it in tables as a single meta field, that make reading and writing much faster and, the most important, **does not create extra rows for each meta** in appropriate meta tables ( "postmeta", "termmeta", "usermeta" ).

## Installation
Download or clone repository to the active theme folder and include it as follows:

    include_once 'all-in-one-meta/loader.php';

## Configuration
**AIOM** uses "aiom" meta key by default, but it can be configured to use other ones:

    AIOM_Config::setup( array(  
      'post_meta_key' => 'post_aiom', // meta key to use in `postmeta` table
      'tax_meta_key'  => 'term_aiom', // meta key to use in `termmeta` table 
      'user_meta_key' => 'user_aiom'  // meta key to use in `usermeta` table
    ) );

## Read Stored Data
**AIOM** comes with 3 methods that allows to easily get already stored data

#### Read post meta
    /**  
     * Get post meta data 
     * @param int     $object_id   Post ID  
     * @param string  $field_name  Field name ( meta key ): can be 
     *                             omitted to get all fields
     * @return mixed  
     */
     aiom_get_post_meta( $object_id, $field_name );

#### Read term meta
    /**  
     * Get term meta data 
     * @param int     $object_id   Term ID  
     * @param string  $field_name  Field name ( meta key ): can be 
     *                             omitted to get all fields
     * @return mixed  
     */
     aiom_get_term_meta( $object_id, $field_name );

#### Read user meta

    /**  
     * Get user meta data 
     * @param int     $object_id   User ID  
     * @param string  $field_name  Field name ( meta key ): can be 
     *                             omitted to get all fields
     * @return mixed  
     */
     aiom_get_user_meta( $object_id, $field_name );

## Attach fields to object

Fields can be attached to specific object ( post type, taxonomy term, user ) as follows:

    // Post types
    new AIOM_Post_Metabox( $config, $callback );
    
    // Taxonomy terms
    new AIOM_Taxonomy_Metabox( $config, $callback );
    
    // User
    new AIOM_User_Metabox( $config, $callback );
    /* 
     * array     $config    An array with metabox configuration
     * callable  $callback  A callback which provides fields structure array
     */
      
#### $config

    // post meta box
    $config = array(
        /*
        * string|int
        * Required: Meta box unique ID
        */
        'id'        => 'post-meta-box-id',
        
        /* 
        * string
        * Required: Meta box title
        */
        'title'     => esc_html__( 'Post meta box title', 'textdomain' ),
        
        /* 
        * string|array
        * Required: Post types to attach meta box to
        *           single: 'post' or array( 'post' )
        *           multiple: array( 'post', 'page', 'any_custom_post_type' )
        */
        'post_type' => array( 'post' ),
        
        /* 
        * string
        * Optional. The context within the screen where the boxes should display: 
        *           'normal', 'side', 'advanced'
        'context'   => 'normal',
        
        /* 
        * string
        * Optional. The priority within the context where the boxes should show:
        *           'default', 'high', 'low'
        */
        'priority'  => 'high',  
    );
    
    
    // term meta box
    $config = array(
        /*
        * string|int
        * Required: Meta box unique ID
        */ 
        'id'       => 'term-meta-box-id',
        
        /* 
        * string
        * Required: Meta box title
        */
        'title'    => esc_html__( 'Boombox Category Advanced Fields', 'boombox' ),
        
        /* 
        * string|array
        * Required: Taxonomies to attach meta box to
        *           single: 'category' or array( 'category' )
        *           multiple: array( 'category', 'tag', 'any_custom_taxonomy' )
        */
        'taxonomy' => array( 'category' )  
    );
    
    
    // user meta box
    $config = array(
        /*
        * string|int
        * Required: Meta box unique ID
        */
        'id'       => 'user-meta-box-id',  
        
        /* 
        * string
        * Required: Meta box title
        */
        'title'    => esc_html__( 'User meta box title', 'textdomain' ),  
        
        /* string|array 
        * Required: Minimum user role to use:
        *           all user roles: '*' or array( '*' )
        *           specific role(s): 'administrator' or array( 'administrator', 'editor' )
        */
        'role'     => array( '*' )  
    );


#### $callback
This callback will be automatically called to get fields structure when needed: A basic example looks like:

    function get_fields_structure() {
        return array(  
            // Single tab
            'single_tab' => array(  
                'title'  => esc_html__( 'Single Tab', 'boombox' ),  
                'active' => true,  
                'icon'   => '',  
                'order'  => 10,  
                'fields' => array(  
                    // Single tab field  
                    'single_tab_field' => array(  
                        'name'     => 'single_tab_field',  
                        'type'     => 'gallery',  
                        'order'    => 10,  
                        'default'  => array(),  
                    ),
                    // other fields for this tab go here 
                ) 
            )
            // other tabs go here  
        );
    }

**NOTE:** All fields should be wrapped within a tab. In case of single tab, the tab itself will not be rendered. As soon as there will be more than one tab, the tabs will be rendered as tabs and the fields will be rendered in the tab they belongs to.

## Tabs configuration
Tabs a represented as key-value pairs in fields configuration array, where the keys are the tabs ids and the values are the tabs configuration. A example below shows the tab basic configuration:

    function get_fields_structure() {
        return array(  
            // Single tab
            'single_tab' => array(
            
                /*
                 * string
                 * Optional: Tab title
                 */
                'title'  => esc_html__( 'Single Tab', 'boombox' ), 
                
                /*
                 * bool
                 * Optional: Whether tab should be active or not. Default: false
                 */
                'active' => false, 
                
                /*
                 * string
                 * Any HTML to use as tab icon: Default: empty string
                 */ 
                'icon'   => '', 
                
                /*
                 * integer
                 * Tab order value: Default 10. Lower values will be rendered first
                 */ 
                'order'  => 10,
                
                /*
                 * array
                 * Required: Tab fields
                 */  
                'fields' => array(  
                    // fields for this tab go here 
                ) 
            )
            // other tabs go here  
        );
    }

## Fields configuration
Fields configuration within a tab "fields" property is represented as an array with key-value pairs, where the key is the meta key of the field and the value is an array with the field configuration. **AIOM** supports multiple field types. Please see "Fields types" section for more information about every type of field.

## Field types

#### Checkbox field

    ...
    'fields' => array(
        'meta_key_name' => array(
            /*
            * string
            * Required: Field type
            */  
            'type' => 'checkbox',
            
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
            
            /*
            * string
            * Optional: Small description about field. Default: empty string
            */
            'description' => esc_html__( 'Meta field description', 'textdomain' ),
            
            /*
            * string
            * Optional: Text to print next to checkbox. Default: empty string
            */
            'text'    => ''
            
            /*
            * integer
            * Optional: Value to store in database
            */
            'val'    => 1,
            
            /*
            * integer | string
            * Required: Default value for the field.
            */ 
            'default' => 1,
            
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
            
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
            
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
        
            /*
            * string | array
            * Optional: Additional classes input field.
            */  
            'class' => '',
            
            /*
            * array
            * Optional: Additional HTML attributes for input field, 
            *           where the keys as the HTML attributes and the values are 
            *           the HTML attribute value.
            */
            'attributes' => array( 'data-index' => 1 ),
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
        
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
            
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: null
            */
            'sanitize_callback' => null,
            
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
        
            /*
            * array
            * Optional: Add field data to appropriate list table in admin panel
            */
            'table_col' => array(
                /*
                * integer
                * Optional: Field order in appropriate list table columns.
                */
                'order'    => 10,
                
                /*
                * string
                * Required: Column heading in appropriate list table.
                */
                'heading'  => 'List Table Column Heading',
                
                /*
                * callable
                * Required: A callback to render field value within appropriate list
                *           table row
                * @param mixed  $value     Field value
                * @param string $field_id  Field ID
                * @param int    $object_id WordPress object ID ( post ID, term ID, user ID )
                * @param array  $field     Field configuration
                * @return mixed Returned value will be echoed in appropriate row column
                */
                'callback' => function( $value, $field_id, $object_id, $field ) {
                    return 'Field Value';
                }
            ),
            
            /*
            * array
            * Optional: Control field appearance base of other field value or
            *           combination of other fields values: Default: null
            */
            'active_callback' => array(
                /*
                * string
                * Optional: The operand to use comparing multiple field values
                */
                'relation' => 'AND',
                
                /*
                * Required: First superior field configuration
                * array {
                *      string  field_id Field meta key
                *      mixed   value    Field value
                *      compare string   Comparison opertor: 
                *                       ===      equal ( strict mode ), 
                *                       == or =, equal 
                *                       !==,     not equal ( strict mode )
                *                       !=,      not equal
                *                       >=,      great or equal
                *                       <=,      lower or equal
                *                       >,       great
                *                       <,       lower
                *                       IN,      in array
                *                       NOT IN   not in array
                * }
                */
                array(
                    'field_id' => 'other_field_1',
                    'value'    => 'some-value',
                    'compare'  => '='
                ),
                array(
                    'field_id' => 'other_field_2',
                    'value'    => 'another-value',
                    'compare'  => '!='
                )
            )
        ),
        // other fields go here
    ),
    ...

#### Color field

    ...
    'fields' => array(
        'meta_key_name' => array(
            /*
            * string
            * Required: Field type
            */  
            'type' => 'color',
        
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
        
            /*
            * string
            * Optional: Small description about field. Default: empty string
            */
            'description' => esc_html__( 'Meta field description', 'textdomain' ),
        
        
            /*
            * integer | string
            * Required: Default value for the field.
            */ 
            'default' => 1,
            
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
            
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
            
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
            
            /*
            * string | array
            * Optional: Additional classes input field.
            */  
            'class' => '',
            
            /*
            * array
            * Optional: Additional HTML attributes for input field, 
            *           where the keys as the HTML attributes and the values are 
            *           the HTML attribute value.
            */
            'attributes' => array( 'data-index' => 1 ),
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
            
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
            
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: 'sanitize_text_field'
            */
            'sanitize_callback' => null,
            
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
            
            /*
            * array
            * Optional: Add field data to appropriate list table in admin panel
            */
            'table_col' => array(
                /*
                * integer
                * Optional: Field order in appropriate list table columns.
                */
                'order'    => 10,
                
                /*
                * string
                * Required: Column heading in appropriate list table.
                */
                'heading'  => 'List Table Column Heading',
                
                /*
                * callable
                * Required: A callback to render field value within appropriate list
                *           table row
                * @param mixed  $value     Field value
                * @param string $field_id  Field ID
                * @param int    $object_id WordPress object ID ( post ID, term ID, user ID )
                * @param array  $field     Field configuration
                * @return mixed Returned value will be echoed in appropriate row column
                */
                'callback' => function( $value, $field_id, $object_id, $field ) {
                    return 'Field Value';
                }
            ),
            
            /*
            * array
            * Optional: Control field appearance base of other field value or
            *           combination of other fields values: Default: null
            */
            'active_callback' => array(
                /*
                * string
                * Optional: The operand to use comparing multiple field values
                */
                'relation' => 'AND',
                
                /*
                * Required: First superior field configuration
                * array {
                *      string  field_id Field meta key
                *      mixed   value    Field value
                *      compare string   Comparison opertor: 
                *                       ===      equal ( strict mode ), 
                *                       == or =, equal 
                *                       !==,     not equal ( strict mode )
                *                       !=,      not equal
                *                       >=,      great or equal
                *                       <=,      lower or equal
                *                       >,       great
                *                       <,       lower
                *                       IN,      in array
                *                       NOT IN   not in array
                * }
                */
                array(
                    'field_id' => 'other_field_1',
                    'value'    => 'some-value',
                    'compare'  => '='
                ),
                array(
                    'field_id' => 'other_field_2',
                    'value'    => 'another-value',
                    'compare'  => '!='
                )
            )
        ),
        // other fields go here
    ),
    ...

#### Multicolor field

    ...
    'fields' => array(
        'meta_key_name' => array(
            
            /*
            * string
            * Required: Field type
            */  
            'type' => 'multicolor',
              
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
            
            /*
            * string
            * Optional: Small description about field. Default: empty string
            */
            'description' => esc_html__( 'Meta field description', 'textdomain' ),
            
            /*
            * array | callback
            * Required: An array or a callback returning an array with 
            *           key-value pairs for each color input field, where 
            *           the keys are the unique ids for this field and 
            *           the values are labels for each color input. 
            */
            'choices' => array(  
                'main_color'      => esc_html__( 'Main color', 'textdomain' ),  
                'secondary_color' => esc_html__( 'Secondary color', 'textdomain' ),  
                'link_color'      => esc_html__( 'Link color', 'textdomain' )  
            ),
            
            /*
            * array
            * Required: Default values for each color field.
            */ 
            'default' => array(  
                'main_color'      => '#00ff00',  
                'secondary_color' => '#0000ff',  
                'link_color'      => '#ff0000',  
            ),
              
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
              
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
              
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
              
            /*
            * string | array
            * Optional: Additional classes for each color input field.
            */  
            'class' => '',
              
            /*
            * array
            * Optional: Additional HTML attributes for each color input field, 
            *           where the keys as the HTML attributes and the values are 
            *           the HTML attribute value.
            */
            'attributes' => array( 'data-index' => 1 ),
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
              
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
              
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: null
            */
            'sanitize_callback' => null,
               
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
               
                /*
                * array
                * Optional: Add field data to appropriate list table in admin panel
                */
                'table_col' => array(
                    /*
                     * integer
                     * Optional: Field order in appropriate list table columns.
                     */
                    'order'    => 10,
                    
                    /*
                     * string
                     * Required: Column heading in appropriate list table.
                     */
                    'heading'  => 'List Table Column Heading',
                    
                    /*
                     * callable
                     * Required: A callback to render field value within appropriate list
                     *           table row
                     * @param mixed  $value     Field value
                     * @param string $field_id  Field ID
                     * @param int    $object_id WordPress object ID ( post ID, term ID, user ID )
                     * @param array  $field     Field configuration
                     * @return mixed Returned value will be echoed in appropriate row column
                     */
                    'callback' => function( $value, $field_id, $object_id, $field ) {
                        return 'Field Value';
                    }
                ),
               
                /*
                * array
                * Optional: Control field appearance base of other field value or
                *           combination of other fields values: Default: null
                */
                'active_callback' => array(
                    /*
                     * string
                     * Optional: The operand to use comparing multiple field values
                     */
                    'relation' => 'AND',
                    
                    /*
                     * Required: First superior field configuration
                     * array {
                     *      string  field_id Field meta key
                     *      mixed   value    Field value
                     *      compare string   Comparison opertor: 
                     *                       ===      equal ( strict mode ), 
                     *                       == or =, equal 
                     *                       !==,     not equal ( strict mode )
                     *                       !=,      not equal
                     *                       >=,      great or equal
                     *                       <=,      lower or equal
                     *                       >,       great
                     *                       <,       lower
                     *                       IN,      in array
                     *                       NOT IN   not in array
                     * }
                     */
                    array(
                        'field_id' => 'other_field_1',
                        'value'    => 'some-value',
                        'compare'  => '='
                    ),
                    array(
                        'field_id' => 'other_field_2',
                        'value'    => 'another-value',
                        'compare'  => '!='
                    )
                )
        ),
        // other fields go here
    ),
    ...

#### Custom HTML field

    ...
    'fields' => array(
        'meta_key_name' => array(
            /*
            * string
            * Required: Field type
            */  
            'type' => 'custom',
            
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
            
            
            /*
            * string
            * Required: HTML to render.
            */ 
            'html' => '',
            
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
            
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
            
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
            
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
            
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: null
            */
            'sanitize_callback' => null,
            
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
    
            /*
            * array
            * Optional: Control field appearance base of other field value or
            *           combination of other fields values: Default: null
            */
            'active_callback' => array(
                /*
                * string
                * Optional: The operand to use comparing multiple field values
                */
                'relation' => 'AND',
                
                /*
                * Required: First superior field configuration
                * array {
                *      string  field_id Field meta key
                *      mixed   value    Field value
                *      compare string   Comparison opertor: 
                *                       ===      equal ( strict mode ), 
                *                       == or =, equal 
                *                       !==,     not equal ( strict mode )
                *                       !=,      not equal
                *                       >=,      great or equal
                *                       <=,      lower or equal
                *                       >,       great
                *                       <,       lower
                *                       IN,      in array
                *                       NOT IN   not in array
                * }
                */
                array(
                    'field_id' => 'other_field_1',
                    'value'    => 'some-value',
                    'compare'  => '='
                ),
                array(
                    'field_id' => 'other_field_2',
                    'value'    => 'another-value',
                    'compare'  => '!='
                )
            )
        ),
        // other fields go here
    ),
    ...

#### Date field

    'fields' => array(
        'meta_key_name' => array(
            /*
            * string
            * Required: Field type
            */  
            'type' => 'date',
            
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
            
            /*
            * string
            * Optional: Small description about field. Default: empty string
            */
            'description' => esc_html__( 'Meta field description', 'textdomain' ),
            
            
            /*
            * integer | string
            * Required: Default value for the field.
            */ 
            'default' => '',
            
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
    
            /*
            * array
            * Optional: JS configuration for date field. @see [datepicker](http://api.jqueryui.com/datepicker/) for additional
            *           information
            */
            'js' => array(
                'changeMonth' => 1,  
                'changeYear'  => 1,  
                'altFormat'   => 'yy-mm-dd',  
                'yearRange'   => 'c-50:c',  
                'minDate'     => 'c-50y',  
                'maxDate'     => time()
            ),
    
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
            
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
    
            /*
            * string | array
            * Optional: Additional classes input field.
            */  
            'class' => '',
            
            /*
            * array
            * Optional: Additional HTML attributes for input field, 
            *           where the keys as the HTML attributes and the values are 
            *           the HTML attribute value.
            */
            'attributes' => array( 'data-index' => 1 ),
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
    
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
            
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: null
            */
            'sanitize_callback' => null,
            
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
    
            /*
            * array
            * Optional: Add field data to appropriate list table in admin panel
            */
            'table_col' => array(
                /*
                * integer
                * Optional: Field order in appropriate list table columns.
                */
                'order'    => 10,
        
                /*
                * string
                * Required: Column heading in appropriate list table.
                */
                'heading'  => 'List Table Column Heading',
                
                /*
                * callable
                * Required: A callback to render field value within appropriate list
                *           table row
                * @param mixed  $value     Field value
                * @param string $field_id  Field ID
                * @param int    $object_id WordPress object ID ( post ID, term ID, user ID )
                * @param array  $field     Field configuration
                * @return mixed Returned value will be echoed in appropriate row column
                */
                'callback' => function( $value, $field_id, $object_id, $field ) {
                    return 'Field Value';
                }
            ),
    
            /*
            * array
            * Optional: Control field appearance base of other field value or
            *           combination of other fields values: Default: null
            */
            'active_callback' => array(
                /*
                * string
                * Optional: The operand to use comparing multiple field values
                */
                'relation' => 'AND',
                
                /*
                * Required: First superior field configuration
                * array {
                *      string  field_id Field meta key
                *      mixed   value    Field value
                *      compare string   Comparison opertor: 
                *                       ===      equal ( strict mode ), 
                *                       == or =, equal 
                *                       !==,     not equal ( strict mode )
                *                       !=,      not equal
                *                       >=,      great or equal
                *                       <=,      lower or equal
                *                       >,       great
                *                       <,       lower
                *                       IN,      in array
                *                       NOT IN   not in array
                * }
                */
                array(
                    'field_id' => 'other_field_1',
                    'value'    => 'some-value',
                    'compare'  => '='
                ),
                array(
                    'field_id' => 'other_field_2',
                    'value'    => 'another-value',
                    'compare'  => '!='
                )
            )
        ),
        // other fields go here
    ),
    ...

#### Gallery field

    ...
    'fields' => array(
        'meta_key_name' => array(
            /*
            * string
            * Required: Field type
            */  
            'type' => 'gallery',
            
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
            
            /*
            * string
            * Optional: Small description about field. Default: empty string
            */
            'description' => esc_html__( 'Meta field description', 'textdomain' ),
            
            /*
            * integer | string
            * Required: Default value for the field.
            */ 
            'default' => array(),
            
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
            
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
            
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
    
            /*
            * string | array
            * Optional: Additional classes input field.
            */  
            'class' => '',
            
            /*
            * array
            * Optional: Additional HTML attributes for input field, 
            *           where the keys as the HTML attributes and the values are 
            *           the HTML attribute value.
            */
            'attributes' => array( 'data-index' => 1 ),
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
    
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
            
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: null
            */
            'sanitize_callback' => null,
            
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
    
            /*
            * array
            * Optional: Add field data to appropriate list table in admin panel
            */
            'table_col' => array(
                /*
                * integer
                * Optional: Field order in appropriate list table columns.
                */
                'order'    => 10,
        
                /*
                * string
                * Required: Column heading in appropriate list table.
                */
                'heading'  => 'List Table Column Heading',
                
                /*
                * callable
                * Required: A callback to render field value within appropriate list
                *           table row
                * @param mixed  $value     Field value
                * @param string $field_id  Field ID
                * @param int    $object_id WordPress object ID ( post ID, term ID, user ID )
                * @param array  $field     Field configuration
                * @return mixed Returned value will be echoed in appropriate row column
                */
                'callback' => function( $value, $field_id, $object_id, $field ) {
                    return 'Field Value';
                }
            ),
    
            /*
            * array
            * Optional: Control field appearance base of other field value or
            *           combination of other fields values: Default: null
            */
            'active_callback' => array(
                /*
                * string
                * Optional: The operand to use comparing multiple field values
                */
                'relation' => 'AND',
                
                /*
                * Required: First superior field configuration
                * array {
                *      string  field_id Field meta key
                *      mixed   value    Field value
                *      compare string   Comparison opertor: 
                *                       ===      equal ( strict mode ), 
                *                       == or =, equal 
                *                       !==,     not equal ( strict mode )
                *                       !=,      not equal
                *                       >=,      great or equal
                *                       <=,      lower or equal
                *                       >,       great
                *                       <,       lower
                *                       IN,      in array
                *                       NOT IN   not in array
                * }
                */
                array(
                    'field_id' => 'other_field_1',
                    'value'    => 'some-value',
                    'compare'  => '='
                ),
                array(
                    'field_id' => 'other_field_2',
                    'value'    => 'another-value',
                    'compare'  => '!='
                )
            )
        ),
        // other fields go here
    ),
    ... 

#### Image field

    ...
    'fields' => array(
        'meta_key_name' => array(
            /*
            * string
            * Required: Field type
            */  
            'type' => 'image',
            
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
            
            /*
            * string
            * Optional: Small description about field. Default: empty string
            */
            'description' => esc_html__( 'Meta field description', 'textdomain' ),
            
            
            /*
            * integer | string
            * Required: Default value for the field.
            */ 
            'default' => '',
            
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
            
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
            
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
    
            /*
            * string | array
            * Optional: Additional classes input field.
            */  
            'class' => '',
            
            /*
            * array
            * Optional: Additional HTML attributes for input field, 
            *           where the keys as the HTML attributes and the values are 
            *           the HTML attribute value.
            */
            'attributes' => array( 'data-index' => 1 ),
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
    
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
            
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: 'sanitize_text_field'
            */
            'sanitize_callback' => null,
            
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
    
            /*
            * array
            * Optional: Add field data to appropriate list table in admin panel
            */
            'table_col' => array(
                /*
                * integer
                * Optional: Field order in appropriate list table columns.
                */
                'order'    => 10,
        
                /*
                * string
                * Required: Column heading in appropriate list table.
                */
                'heading'  => 'List Table Column Heading',
                
                /*
                * callable
                * Required: A callback to render field value within appropriate list
                *           table row
                * @param mixed  $value     Field value
                * @param string $field_id  Field ID
                * @param int    $object_id WordPress object ID ( post ID, term ID, user ID )
                * @param array  $field     Field configuration
                * @return mixed Returned value will be echoed in appropriate row column
                */
                'callback' => function( $value, $field_id, $object_id, $field ) {
                    return 'Field Value';
                }
            ),
    
            /*
            * array
            * Optional: Control field appearance base of other field value or
            *           combination of other fields values: Default: null
            */
            'active_callback' => array(
                /*
                * string
                * Optional: The operand to use comparing multiple field values
                */
                'relation' => 'AND',
                
                /*
                * Required: First superior field configuration
                * array {
                *      string  field_id Field meta key
                *      mixed   value    Field value
                *      compare string   Comparison opertor: 
                *                       ===      equal ( strict mode ), 
                *                       == or =, equal 
                *                       !==,     not equal ( strict mode )
                *                       !=,      not equal
                *                       >=,      great or equal
                *                       <=,      lower or equal
                *                       >,       great
                *                       <,       lower
                *                       IN,      in array
                *                       NOT IN   not in array
                * }
                */
                array(
                    'field_id' => 'other_field_1',
                    'value'    => 'some-value',
                    'compare'  => '='
                ),
                array(
                    'field_id' => 'other_field_2',
                    'value'    => 'another-value',
                    'compare'  => '!='
                )
            )
        ),
        // other fields go here
    ),
    ...

#### Multicheck field

    ...
    'fields' => array(
        'meta_key_name' => array(
            
            /*
            * string
            * Required: Field type
            */  
            'type' => 'multicheck',
              
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
            
            /*
            * string
            * Optional: Small description about field. Default: empty string
            */
            'description' => esc_html__( 'Meta field description', 'textdomain' ),
            
            /*
            * array | callback
            * Required: An array or a callback returning an array with 
            *           key-value pairs for input field, where 
            *           the keys are the unique ids for this field and 
            *           the values are labels for each input. 
            */
            'choices' => array(  
                'render_title'      => esc_html__( 'Check to render post title', 'textdomain' ),  
                'render_thumbnail'  => esc_html__( 'Check to render post thumbnail', 'textdomain' ),  
                'render_author'     => esc_html__( 'Check to render post author info', 'textdomain' )  
            ),
            
            /*
            * array
            * Required: Default values for each input field.
            */ 
            'default' => array( 'render_title', 'render_author' ),
              
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
              
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
              
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
              
            /*
            * string | array
            * Optional: Additional classes for each input field.
            */  
            'class' => '',
              
            /*
            * array
            * Optional: Additional HTML attributes for each input field, 
            *           where the keys as the HTML attributes and the values are 
            *           the HTML attribute value.
            */
            'attributes' => array( 'data-index' => 1 ),
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
              
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
              
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: null
            */
            'sanitize_callback' => null,
               
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
               
            /*
            * array
            * Optional: Add field data to appropriate list table in admin panel
            */
            'table_col' => array(
                /*
                 * integer
                 * Optional: Field order in appropriate list table columns.
                 */
                'order'    => 10,
                
                /*
                 * string
                 * Required: Column heading in appropriate list table.
                 */
                'heading'  => 'List Table Column Heading',
                
                /*
                 * callable
                 * Required: A callback to render field value within appropriate list
                 *           table row
                 * @param mixed  $value     Field value
                 * @param string $field_id  Field ID
                 * @param int    $object_id WordPress object ID ( post ID, term ID, user ID )
                 * @param array  $field     Field configuration
                 * @return mixed Returned value will be echoed in appropriate row column
                 */
                'callback' => function( $value, $field_id, $object_id, $field ) {
                    return 'Field Value';
                }
            ),
           
            /*
            * array
            * Optional: Control field appearance base of other field value or
            *           combination of other fields values: Default: null
            */
            'active_callback' => array(
                /*
                 * string
                 * Optional: The operand to use comparing multiple field values
                 */
                'relation' => 'AND',
                
                /*
                 * Required: First superior field configuration
                 * array {
                 *      string  field_id Field meta key
                 *      mixed   value    Field value
                 *      compare string   Comparison opertor: 
                 *                       ===      equal ( strict mode ), 
                 *                       == or =, equal 
                 *                       !==,     not equal ( strict mode )
                 *                       !=,      not equal
                 *                       >=,      great or equal
                 *                       <=,      lower or equal
                 *                       >,       great
                 *                       <,       lower
                 *                       IN,      in array
                 *                       NOT IN   not in array
                 * }
                 */
                array(
                    'field_id' => 'other_field_1',
                    'value'    => 'some-value',
                    'compare'  => '='
                ),
                array(
                    'field_id' => 'other_field_2',
                    'value'    => 'another-value',
                    'compare'  => '!='
                )
            )
        ),
        // other fields go here
    ),
    ...

#### Number field

    ...
    'fields' => array(
        'meta_key_name' => array(
            /*
            * string
            * Required: Field type
            */  
            'type' => 'number',
        
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
        
            /*
            * string
            * Optional: Small description about field. Default: empty string
            */
            'description' => esc_html__( 'Meta field description', 'textdomain' ),
        
        
            /*
            * integer | string
            * Required: Default value for the field.
            */ 
            'default' => '',
            
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
            
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
            
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
            
            /*
            * string | array
            * Optional: Additional classes input field.
            */  
            'class' => '',
            
            /*
            * array
            * Optional: Additional HTML attributes for input field, 
            *           where the keys as the HTML attributes and the values are 
            *           the HTML attribute value.
            */
            'attributes' => array( 'min' => 0, 'step' => 1 ),
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
            
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
            
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: 'sanitize_text_field'
            */
            'sanitize_callback' => null,
            
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
            
            /*
            * array
            * Optional: Add field data to appropriate list table in admin panel
            */
            'table_col' => array(
                /*
                * integer
                * Optional: Field order in appropriate list table columns.
                */
                'order'    => 10,
                
                /*
                * string
                * Required: Column heading in appropriate list table.
                */
                'heading'  => 'List Table Column Heading',
                
                /*
                * callable
                * Required: A callback to render field value within appropriate list
                *           table row
                * @param mixed  $value     Field value
                * @param string $field_id  Field ID
                * @param int    $object_id WordPress object ID ( post ID, term ID, user ID )
                * @param array  $field     Field configuration
                * @return mixed Returned value will be echoed in appropriate row column
                */
                'callback' => function( $value, $field_id, $object_id, $field ) {
                    return 'Field Value';
                }
            ),
            
            /*
            * array
            * Optional: Control field appearance base of other field value or
            *           combination of other fields values: Default: null
            */
            'active_callback' => array(
                /*
                * string
                * Optional: The operand to use comparing multiple field values
                */
                'relation' => 'AND',
                
                /*
                * Required: First superior field configuration
                * array {
                *      string  field_id Field meta key
                *      mixed   value    Field value
                *      compare string   Comparison opertor: 
                *                       ===      equal ( strict mode ), 
                *                       == or =, equal 
                *                       !==,     not equal ( strict mode )
                *                       !=,      not equal
                *                       >=,      great or equal
                *                       <=,      lower or equal
                *                       >,       great
                *                       <,       lower
                *                       IN,      in array
                *                       NOT IN   not in array
                * }
                */
                array(
                    'field_id' => 'other_field_1',
                    'value'    => 'some-value',
                    'compare'  => '='
                ),
                array(
                    'field_id' => 'other_field_2',
                    'value'    => 'another-value',
                    'compare'  => '!='
                )
            )
        ),
        // other fields go here
    ),
    ...

#### Radio field

    ...
    'fields' => array(
        'meta_key_name' => array(
            
            /*
            * string
            * Required: Field type
            */  
            'type' => 'radio',
              
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
            
            /*
            * string
            * Optional: Small description about field. Default: empty string
            */
            'description' => esc_html__( 'Meta field description', 'textdomain' ),
            
            /*
            * array | callback
            * Required: An array or a callback returning an array with 
            *           key-value pairs for input field, where 
            *           the keys are the unique ids for this field and 
            *           the values are labels for each input. 
            */
            'choices' => array(  
                'variation_1'  => esc_html__( 'Variation 1', 'textdomain' ),  
                'variation_2'  => esc_html__( 'Variation 2', 'textdomain' ),  
                'variation_3'  => esc_html__( 'Variation 3', 'textdomain' )  
            ),
            
            /*
            * string
            * Required: Default value.
            */ 
            'default' => 'variation_2',
            
            /**
            * string
            * Radio input display axis: 'horisontal' or 'vertical'. Default: 'horisontal'
            */
            'axis' => 'horizontal'
              
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
              
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
              
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
              
            /*
            * string | array
            * Optional: Additional classes for each input field.
            */  
            'class' => '',
              
            /*
            * array
            * Optional: Additional HTML attributes for each input field, 
            *           where the keys as the HTML attributes and the values are 
            *           the HTML attribute value.
            */
            'attributes' => array( 'data-index' => 1 ),
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
              
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
              
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: 'sanitize_text_field'
            */
            'sanitize_callback' => null,
               
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
               
            /*
            * array
            * Optional: Add field data to appropriate list table in admin panel
            */
            'table_col' => array(
                /*
                 * integer
                 * Optional: Field order in appropriate list table columns.
                 */
                'order'    => 10,
                
                /*
                 * string
                 * Required: Column heading in appropriate list table.
                 */
                'heading'  => 'List Table Column Heading',
                
                /*
                 * callable
                 * Required: A callback to render field value within appropriate list
                 *           table row
                 * @param mixed  $value     Field value
                 * @param string $field_id  Field ID
                 * @param int    $object_id WordPress object ID ( post ID, term ID, user ID )
                 * @param array  $field     Field configuration
                 * @return mixed Returned value will be echoed in appropriate row column
                 */
                'callback' => function( $value, $field_id, $object_id, $field ) {
                    return 'Field Value';
                }
            ),
           
            /*
            * array
            * Optional: Control field appearance base of other field value or
            *           combination of other fields values: Default: null
            */
            'active_callback' => array(
                /*
                 * string
                 * Optional: The operand to use comparing multiple field values
                 */
                'relation' => 'AND',
                
                /*
                 * Required: First superior field configuration
                 * array {
                 *      string  field_id Field meta key
                 *      mixed   value    Field value
                 *      compare string   Comparison opertor: 
                 *                       ===      equal ( strict mode ), 
                 *                       == or =, equal 
                 *                       !==,     not equal ( strict mode )
                 *                       !=,      not equal
                 *                       >=,      great or equal
                 *                       <=,      lower or equal
                 *                       >,       great
                 *                       <,       lower
                 *                       IN,      in array
                 *                       NOT IN   not in array
                 * }
                 */
                array(
                    'field_id' => 'other_field_1',
                    'value'    => 'some-value',
                    'compare'  => '='
                ),
                array(
                    'field_id' => 'other_field_2',
                    'value'    => 'another-value',
                    'compare'  => '!='
                )
            )
        ),
        // other fields go here
    ),
    ...

#### Radio Image field

    ...
    'fields' => array(
        'meta_key_name' => array(
            
            /*
            * string
            * Required: Field type
            */  
            'type' => 'radio_image',
              
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
            
            /*
            * string
            * Optional: Small description about field. Default: empty string
            */
            'description' => esc_html__( 'Meta field description', 'textdomain' ),
            
            /*
            * array | callback
            * Required: An array or a callback returning an array with 
            *           key-value pairs for input field, where 
            *           the keys are the unique ids for this field and 
            *           the values are URLs to images representing a single choice. 
            */
            'choices' => array(  
                'type_1' => 'https://example.com/type_1.jpg',  
                'type_2' => 'https://example.com/type_2.jpg',  
                'type_3' => 'https://example.com/type_3.jpg',  
            ),
            
            /*
            * string
            * Required: Default values for each input field.
            */ 
            'default' => 'type_1',
              
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
              
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
              
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
              
            /*
            * string | array
            * Optional: Additional classes for each input field.
            */  
            'class' => '',
              
            /*
            * array
            * Optional: Additional HTML attributes for each input field, 
            *           where the keys as the HTML attributes and the values are 
            *           the HTML attribute value.
            */
            'attributes' => array( 'data-index' => 1 ),
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
              
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
              
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: 'sanitize_text_field'
            */
            'sanitize_callback' => null,
               
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
               
            /*
            * array
            * Optional: Add field data to appropriate list table in admin panel
            */
            'table_col' => array(
                /*
                 * integer
                 * Optional: Field order in appropriate list table columns.
                 */
                'order'    => 10,
                
                /*
                 * string
                 * Required: Column heading in appropriate list table.
                 */
                'heading'  => 'List Table Column Heading',
                
                /*
                 * callable
                 * Required: A callback to render field value within appropriate list
                 *           table row
                 * @param mixed  $value     Field value
                 * @param string $field_id  Field ID
                 * @param int    $object_id WordPress object ID ( post ID, term ID, user ID )
                 * @param array  $field     Field configuration
                 * @return mixed Returned value will be echoed in appropriate row column
                 */
                'callback' => function( $value, $field_id, $object_id, $field ) {
                    return 'Field Value';
                }
            ),
           
            /*
            * array
            * Optional: Control field appearance base of other field value or
            *           combination of other fields values: Default: null
            */
            'active_callback' => array(
                /*
                 * string
                 * Optional: The operand to use comparing multiple field values
                 */
                'relation' => 'AND',
                
                /*
                 * Required: First superior field configuration
                 * array {
                 *      string  field_id Field meta key
                 *      mixed   value    Field value
                 *      compare string   Comparison opertor: 
                 *                       ===      equal ( strict mode ), 
                 *                       == or =, equal 
                 *                       !==,     not equal ( strict mode )
                 *                       !=,      not equal
                 *                       >=,      great or equal
                 *                       <=,      lower or equal
                 *                       >,       great
                 *                       <,       lower
                 *                       IN,      in array
                 *                       NOT IN   not in array
                 * }
                 */
                array(
                    'field_id' => 'other_field_1',
                    'value'    => 'some-value',
                    'compare'  => '='
                ),
                array(
                    'field_id' => 'other_field_2',
                    'value'    => 'another-value',
                    'compare'  => '!='
                )
            )
        ),
        // other fields go here
    ),
    ...

#### Select field

    ...
    'fields' => array(
        'meta_key_name' => array(
            
            /*
            * string
            * Required: Field type
            */  
            'type' => 'select',
              
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
            
            /*
            * string
            * Optional: Small description about field. Default: empty string
            */
            'description' => esc_html__( 'Meta field description', 'textdomain' ),
            
            /*
            * array | callback
            * Required: An array or a callback returning an array with 
            *           key-value pairs for input field, where 
            *           the keys are the unique ids for this field and 
            *           the values are labels for each input. 
            */
            'choices' => array(  
                'variation_1'  => esc_html__( 'Variation 1', 'textdomain' ),  
                'variation_2'  => esc_html__( 'Variation 2', 'textdomain' ),  
                'variation_3'  => esc_html__( 'Variation 3', 'textdomain' )  
            ),
            
            /*
            * string | array
            * Required: Default value. array for multiselect and string - for single select
            */ 
            'default' => 'variation_2',
              
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
              
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
              
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
              
            /*
            * string | array
            * Optional: Additional classes for each input field.
            */  
            'class' => '',
              
            /*
            * array
            * Optional: Additional HTML attributes for each input field, 
            *           where the keys as the HTML attributes and the values are 
            *           the HTML attribute value.
            */
            'attributes' => array( 'multiple' => 'multiple' ),
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
              
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
              
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: null
            */
            'sanitize_callback' => null,
               
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
               
            /*
            * array
            * Optional: Add field data to appropriate list table in admin panel
            */
            'table_col' => array(
                /*
                 * integer
                 * Optional: Field order in appropriate list table columns.
                 */
                'order'    => 10,
                
                /*
                 * string
                 * Required: Column heading in appropriate list table.
                 */
                'heading'  => 'List Table Column Heading',
                
                /*
                 * callable
                 * Required: A callback to render field value within appropriate list
                 *           table row
                 * @param mixed  $value     Field value
                 * @param string $field_id  Field ID
                 * @param int    $object_id WordPress object ID ( post ID, term ID, user ID )
                 * @param array  $field     Field configuration
                 * @return mixed Returned value will be echoed in appropriate row column
                 */
                'callback' => function( $value, $field_id, $object_id, $field ) {
                    return 'Field Value';
                }
            ),
           
            /*
            * array
            * Optional: Control field appearance base of other field value or
            *           combination of other fields values: Default: null
            */
            'active_callback' => array(
                /*
                 * string
                 * Optional: The operand to use comparing multiple field values
                 */
                'relation' => 'AND',
                
                /*
                 * Required: First superior field configuration
                 * array {
                 *      string  field_id Field meta key
                 *      mixed   value    Field value
                 *      compare string   Comparison opertor: 
                 *                       ===      equal ( strict mode ), 
                 *                       == or =, equal 
                 *                       !==,     not equal ( strict mode )
                 *                       !=,      not equal
                 *                       >=,      great or equal
                 *                       <=,      lower or equal
                 *                       >,       great
                 *                       <,       lower
                 *                       IN,      in array
                 *                       NOT IN   not in array
                 * }
                 */
                array(
                    'field_id' => 'other_field_1',
                    'value'    => 'some-value',
                    'compare'  => '='
                ),
                array(
                    'field_id' => 'other_field_2',
                    'value'    => 'another-value',
                    'compare'  => '!='
                )
            )
        ),
        // other fields go here
    ),
    ...

#### Text field

    ...
    'fields' => array(
        'meta_key_name' => array(
            /*
            * string
            * Required: Field type
            */  
            'type' => 'text',
            
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
            
            /*
            * string
            * Optional: Small description about field. Default: empty string
            */
            'description' => esc_html__( 'Meta field description', 'textdomain' ),
            
            /*
            * string
            * Required: Default value for the field.
            */ 
            'default' => '',
            
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
            
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
            
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
        
            /*
            * string | array
            * Optional: Additional classes input field.
            */  
            'class' => '',
            
            /*
            * array
            * Optional: Additional HTML attributes for input field, 
            *           where the keys as the HTML attributes and the values are 
            *           the HTML attribute value.
            */
            'attributes' => array( 'data-index' => 1 ),
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
        
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
            
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: 'sanitize_text_field'
            */
            'sanitize_callback' => null,
            
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
        
            /*
            * array
            * Optional: Add field data to appropriate list table in admin panel
            */
            'table_col' => array(
                /*
                * integer
                * Optional: Field order in appropriate list table columns.
                */
                'order'    => 10,
                
                /*
                * string
                * Required: Column heading in appropriate list table.
                */
                'heading'  => 'List Table Column Heading',
                
                /*
                * callable
                * Required: A callback to render field value within appropriate list
                *           table row
                * @param mixed  $value     Field value
                * @param string $field_id  Field ID
                * @param int    $object_id WordPress object ID ( post ID, term ID, user ID )
                * @param array  $field     Field configuration
                * @return mixed Returned value will be echoed in appropriate row column
                */
                'callback' => function( $value, $field_id, $object_id, $field ) {
                    return 'Field Value';
                }
            ),
            
            /*
            * array
            * Optional: Control field appearance base of other field value or
            *           combination of other fields values: Default: null
            */
            'active_callback' => array(
                /*
                * string
                * Optional: The operand to use comparing multiple field values
                */
                'relation' => 'AND',
                
                /*
                * Required: First superior field configuration
                * array {
                *      string  field_id Field meta key
                *      mixed   value    Field value
                *      compare string   Comparison opertor: 
                *                       ===      equal ( strict mode ), 
                *                       == or =, equal 
                *                       !==,     not equal ( strict mode )
                *                       !=,      not equal
                *                       >=,      great or equal
                *                       <=,      lower or equal
                *                       >,       great
                *                       <,       lower
                *                       IN,      in array
                *                       NOT IN   not in array
                * }
                */
                array(
                    'field_id' => 'other_field_1',
                    'value'    => 'some-value',
                    'compare'  => '='
                ),
                array(
                    'field_id' => 'other_field_2',
                    'value'    => 'another-value',
                    'compare'  => '!='
                )
            )
        ),
        // other fields go here
    ),
    ...

#### Textarea field

    ...
    'fields' => array(
        'meta_key_name' => array(
            /*
            * string
            * Required: Field type
            */  
            'type' => 'textarea',
            
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
            
            /*
            * string
            * Optional: Small description about field. Default: empty string
            */
            'description' => esc_html__( 'Meta field description', 'textdomain' ),
            
            /*
            * string
            * Required: Default value for the field.
            */ 
            'default' => '',
            
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
            
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
            
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
        
            /*
            * string | array
            * Optional: Additional classes input field.
            */  
            'class' => '',
            
            /*
            * array
            * Optional: Additional HTML attributes for input field, 
            *           where the keys as the HTML attributes and the values are 
            *           the HTML attribute value.
            */
            'attributes' => array( 'data-index' => 1 ),
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
        
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
            
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: 'sanitize_textarea_field'
            */
            'sanitize_callback' => null,
            
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
        
            /*
            * array
            * Optional: Add field data to appropriate list table in admin panel
            */
            'table_col' => array(
                /*
                * integer
                * Optional: Field order in appropriate list table columns.
                */
                'order'    => 10,
                
                /*
                * string
                * Required: Column heading in appropriate list table.
                */
                'heading'  => 'List Table Column Heading',
                
                /*
                * callable
                * Required: A callback to render field value within appropriate list
                *           table row
                * @param mixed  $value     Field value
                * @param string $field_id  Field ID
                * @param int    $object_id WordPress object ID ( post ID, term ID, user ID )
                * @param array  $field     Field configuration
                * @return mixed Returned value will be echoed in appropriate row column
                */
                'callback' => function( $value, $field_id, $object_id, $field ) {
                    return 'Field Value';
                }
            ),
            
            /*
            * array
            * Optional: Control field appearance base of other field value or
            *           combination of other fields values: Default: null
            */
            'active_callback' => array(
                /*
                * string
                * Optional: The operand to use comparing multiple field values
                */
                'relation' => 'AND',
                
                /*
                * Required: First superior field configuration
                * array {
                *      string  field_id Field meta key
                *      mixed   value    Field value
                *      compare string   Comparison opertor: 
                *                       ===      equal ( strict mode ), 
                *                       == or =, equal 
                *                       !==,     not equal ( strict mode )
                *                       !=,      not equal
                *                       >=,      great or equal
                *                       <=,      lower or equal
                *                       >,       great
                *                       <,       lower
                *                       IN,      in array
                *                       NOT IN   not in array
                * }
                */
                array(
                    'field_id' => 'other_field_1',
                    'value'    => 'some-value',
                    'compare'  => '='
                ),
                array(
                    'field_id' => 'other_field_2',
                    'value'    => 'another-value',
                    'compare'  => '!='
                )
            )
        ),
        // other fields go here
    ),
    ...

#### Text field

    ...
    'fields' => array(
        'meta_key_name' => array(
            /*
            * string
            * Required: Field type
            */  
            'type' => 'text',
            
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
            
            /*
            * string
            * Optional: Small description about field. Default: empty string
            */
            'description' => esc_html__( 'Meta field description', 'textdomain' ),
            
            /*
            * string
            * Required: Default value for the field.
            */ 
            'default' => '',
            
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
            
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
            
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
        
            /*
            * string | array
            * Optional: Additional classes input field.
            */  
            'class' => '',
            
            /*
            * array
            * Optional: Additional HTML attributes for input field, 
            *           where the keys as the HTML attributes and the values are 
            *           the HTML attribute value.
            */
            'attributes' => array( 'data-index' => 1 ),
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
        
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
            
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: 'sanitize_text_field'
            */
            'sanitize_callback' => null,
            
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
        
            /*
            * array
            * Optional: Add field data to appropriate list table in admin panel
            */
            'table_col' => array(
                /*
                * integer
                * Optional: Field order in appropriate list table columns.
                */
                'order'    => 10,
                
                /*
                * string
                * Required: Column heading in appropriate list table.
                */
                'heading'  => 'List Table Column Heading',
                
                /*
                * callable
                * Required: A callback to render field value within appropriate list
                *           table row
                * @param mixed  $value     Field value
                * @param string $field_id  Field ID
                * @param int    $object_id WordPress object ID ( post ID, term ID, user ID )
                * @param array  $field     Field configuration
                * @return mixed Returned value will be echoed in appropriate row column
                */
                'callback' => function( $value, $field_id, $object_id, $field ) {
                    return 'Field Value';
                }
            ),
            
            /*
            * array
            * Optional: Control field appearance base of other field value or
            *           combination of other fields values: Default: null
            */
            'active_callback' => array(
                /*
                * string
                * Optional: The operand to use comparing multiple field values
                */
                'relation' => 'AND',
                
                /*
                * Required: First superior field configuration
                * array {
                *      string  field_id Field meta key
                *      mixed   value    Field value
                *      compare string   Comparison opertor: 
                *                       ===      equal ( strict mode ), 
                *                       == or =, equal 
                *                       !==,     not equal ( strict mode )
                *                       !=,      not equal
                *                       >=,      great or equal
                *                       <=,      lower or equal
                *                       >,       great
                *                       <,       lower
                *                       IN,      in array
                *                       NOT IN   not in array
                * }
                */
                array(
                    'field_id' => 'other_field_1',
                    'value'    => 'some-value',
                    'compare'  => '='
                ),
                array(
                    'field_id' => 'other_field_2',
                    'value'    => 'another-value',
                    'compare'  => '!='
                )
            )
        ),
        // other fields go here
    ),
    ...

#### URL field

    ...
    'fields' => array(
        'meta_key_name' => array(
            /*
            * string
            * Required: Field type
            */  
            'type' => 'url',
            
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
            
            /*
            * string
            * Optional: Small description about field. Default: empty string
            */
            'description' => esc_html__( 'Meta field description', 'textdomain' ),
            
            /*
            * string
            * Required: Default value for the field.
            */ 
            'default' => '',
            
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
            
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
            
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
        
            /*
            * string | array
            * Optional: Additional classes input field.
            */  
            'class' => '',
            
            /*
            * array
            * Optional: Additional HTML attributes for input field, 
            *           where the keys as the HTML attributes and the values are 
            *           the HTML attribute value.
            */
            'attributes' => array( 'data-index' => 1 ),
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
        
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
            
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: 'sanitize_text_field'
            */
            'sanitize_callback' => null,
            
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
        
            /*
            * array
            * Optional: Add field data to appropriate list table in admin panel
            */
            'table_col' => array(
                /*
                * integer
                * Optional: Field order in appropriate list table columns.
                */
                'order'    => 10,
                
                /*
                * string
                * Required: Column heading in appropriate list table.
                */
                'heading'  => 'List Table Column Heading',
                
                /*
                * callable
                * Required: A callback to render field value within appropriate list
                *           table row
                * @param mixed  $value     Field value
                * @param string $field_id  Field ID
                * @param int    $object_id WordPress object ID ( post ID, term ID, user ID )
                * @param array  $field     Field configuration
                * @return mixed Returned value will be echoed in appropriate row column
                */
                'callback' => function( $value, $field_id, $object_id, $field ) {
                    return 'Field Value';
                }
            ),
            
            /*
            * array
            * Optional: Control field appearance base of other field value or
            *           combination of other fields values: Default: null
            */
            'active_callback' => array(
                /*
                * string
                * Optional: The operand to use comparing multiple field values
                */
                'relation' => 'AND',
                
                /*
                * Required: First superior field configuration
                * array {
                *      string  field_id Field meta key
                *      mixed   value    Field value
                *      compare string   Comparison opertor: 
                *                       ===      equal ( strict mode ), 
                *                       == or =, equal 
                *                       !==,     not equal ( strict mode )
                *                       !=,      not equal
                *                       >=,      great or equal
                *                       <=,      lower or equal
                *                       >,       great
                *                       <,       lower
                *                       IN,      in array
                *                       NOT IN   not in array
                * }
                */
                array(
                    'field_id' => 'other_field_1',
                    'value'    => 'some-value',
                    'compare'  => '='
                ),
                array(
                    'field_id' => 'other_field_2',
                    'value'    => 'another-value',
                    'compare'  => '!='
                )
            )
        ),
        // other fields go here
    ),
    ...

#### Video URL

    ...
    'fields' => array(
        'meta_key_name' => array(
            /*
            * string
            * Required: Field type
            */  
            'type' => 'video_url',
            
            /*
            * string
            * Optional: Field label. Default: empty string
            */
            'label' => esc_html__( 'Meta field label', 'textdomain' ),
            
            /*
            * string
            * Optional: Small description about field. Default: empty string
            */
            'description' => esc_html__( 'Meta field description', 'textdomain' ),
            
            /*
            * string
            * Required: Default value for the field.
            */ 
            'default' => '',
            
            /*
            * integer
            * Optional: Control field order within the tab. Default: 10
            */  
            'order' => 10,
            
            /*
            * integer
            * Optional: Control field order within the fields with same
            *           order within the tab. Default: 10
            */
            'sub_order' => 10,
            
            /*
            * bool
            * Optional: Control whether this field should also be 
            *           saved as separate meta row in meta table. 
            *           Usefull, if this field should be used within 
            *           meta queries. Default: false
            */
            'standalone' => false,
        
            /*
            * string | array
            * Optional: Additional classes input field.
            */  
            'class' => '',
            
            /*
            * array
            * Optional: Additional HTML attributes for input field, 
            *           where the keys as the HTML attributes and the values are 
            *           the HTML attribute value.
            */
            'attributes' => array( 'data-index' => 1 ),
            
            /*
            * string | array
            * Optional: Additional classes field wrapper element.
            */
            'wrapper_class' => '',
        
            /*
            * array
            * Optional: Additional HTML attributes for field wrapper element, 
            *           where the keys as the HTML attributes and the 
            *           values are the HTML attribute value.
            */
            'wrapper_attributes' => array( 'data-index' => 1 ),
            
            /*
            * callable
            * Optional: A callback to sanitize the field value before 
            *           storing it to database: Default: null
            */
            'sanitize_callback' => null,
            
            /*
            * callable
            * Optional: A callback to run before rendering field value: 
            *           Default: null
            */
            'render_callback' => null,
        
            /*
            * array
            * Optional: Add field data to appropriate list table in admin panel
            */
            'table_col' => array(
                /*
                * integer
                * Optional: Field order in appropriate list table columns.
                */
                'order'    => 10,
                
                /*
                * string
                * Required: Column heading in appropriate list table.
                */
                'heading'  => 'List Table Column Heading',
                
                /*
                * callable
                * Required: A callback to render field value within appropriate list
                *           table row
                * @param mixed  $value     Field value
                * @param string $field_id  Field ID
                * @param int    $object_id WordPress object ID ( post ID, term ID, user ID )
                * @param array  $field     Field configuration
                * @return mixed Returned value will be echoed in appropriate row column
                */
                'callback' => function( $value, $field_id, $object_id, $field ) {
                    return 'Field Value';
                }
            ),
            
            /*
            * array
            * Optional: Control field appearance base of other field value or
            *           combination of other fields values: Default: null
            */
            'active_callback' => array(
                /*
                * string
                * Optional: The operand to use comparing multiple field values
                */
                'relation' => 'AND',
                
                /*
                * Required: First superior field configuration
                * array {
                *      string  field_id Field meta key
                *      mixed   value    Field value
                *      compare string   Comparison opertor: 
                *                       ===      equal ( strict mode ), 
                *                       == or =, equal 
                *                       !==,     not equal ( strict mode )
                *                       !=,      not equal
                *                       >=,      great or equal
                *                       <=,      lower or equal
                *                       >,       great
                *                       <,       lower
                *                       IN,      in array
                *                       NOT IN   not in array
                * }
                */
                array(
                    'field_id' => 'other_field_1',
                    'value'    => 'some-value',
                    'compare'  => '='
                ),
                array(
                    'field_id' => 'other_field_2',
                    'value'    => 'another-value',
                    'compare'  => '!='
                )
            )
        ),
        // other fields go here
    ),
    ...
