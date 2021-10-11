<?php
/**
 * Zombify Open List Quiz
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_OpenlistQuiz") ) {

    /**
     * Class Zombify_OpenlistQuiz
     */
    class Zombify_OpenlistQuiz extends Zombify_BaseQuiz
    {
        
        /**
         * Quiz view file
         *
         * @var string
         */
        public $view_path = 'openlist';

        /**
         * Quiz slug
         *
         * @var string
         */
        public $slug = 'openlist';

        public $pagination_path = array('list');

        /**
         * Zombify Open List Quiz Structure
         *
         * @return Array
         */
        public function structure(){

            return [
                "title" => [
                    "type" => "field",
                    "name" => "title",
                    "label" => __("Title", "zombify"),
                    "field_type" => "text",
                    "rules" => [
                        "required",
                    ]
                ],
                "image" => [
                    "type" => "field",
                    "name" => "image",
                    "label" => __("Add Thumbnail", "zombify"),
                    "field_type" => "file",
                    "rules" => [
                        "extensions" => "png, jpg, gif, jpeg",
                        "maxSize" => zf_max_upload_size() / 1024
                    ],
                    "use_as_featured" => true,
                ],
                "use_preface" => [
                    "type" => "field",
                    "name" => "use_preface",
                    "label" => __("Add preface", "zombify"),
                    "field_type" => "checkbox",
                    "field_visibility" => ( isset( zombify()->post_main_fields[ $this->slug ]["preface"] ) ? zombify()->post_main_fields[ $this->slug ]["preface"] : 'show' )
                ],
                "preface_description" => [
                    "type" => "field",
                    "name" => "preface_description",
                    "label" => __("Type something here", "zombify"),
                    "field_type" => "textarea",
                    "field_visibility" => ( isset( zombify()->post_main_fields[ $this->slug ]["preface"] ) ? zombify()->post_main_fields[ $this->slug ]["preface"] : 'show' )
                ],
                "use_excerpt" => [
                    "type" => "field",
                    "name" => "use_excerpt",
                    "label" => __("Add excerpt", "zombify"),
                    "field_type" => "checkbox",
                    "field_visibility" => ( isset( zombify()->post_main_fields[ $this->slug ]["excerpt"] ) ? zombify()->post_main_fields[ $this->slug ]["excerpt"] : 'show' )
                ],
                "excerpt_description" => [
                    "type" => "field",
                    "name" => "excerpt_description",
                    "label" => __("Type excerpt here", "zombify"),
                    "field_type" => "textarea",
                    "field_visibility" => ( isset( zombify()->post_main_fields[ $this->slug ]["excerpt"] ) ? zombify()->post_main_fields[ $this->slug ]["excerpt"] : 'show' )
                ],
                "description" => [
                    "type" => "field",
                    "name" => "description",
                    "label" => __("Description", "zombify"),
                    "field_type" => "textarea",
                    "field_visibility" => ( isset( zombify()->post_main_fields[ $this->slug ]["description"] ) ? zombify()->post_main_fields[ $this->slug ]["description"] : 'show' )
                ],
                "list"=>[
                    "type" => "group",
                    "name" => "list",
                    "fields"=> [
                        "post_id" => [
                            "type" => "field",
                            "name" => "post_id",
                            "label" => __("Post ID", "zombify"),
                            "field_type" => "post_id"
                        ],
                        "title" => [
                            "type" => "field",
                            "name" => "title",
                            "label" => __("Title", "zombify"),
                            "field_type" => "text",
                            "rules" => []
                        ],
                        "image" => [
                            "type" => "field",
                            "name" => "image",
                            "label" => __("Browse Image", "zombify"),
                            "field_type" => "file",
                            "rules" => [
                                "extensions" => "png, jpg, gif, jpeg",
                                "maxSize" => zf_max_upload_size() / 1024
                            ],
                            "use_as_featured" => true,
                        ],
                        "original_source" => [
                            "type" => "field",
                            "name" => "original_source",
                            "label" => __("Via", "zombify"),
                            "field_type" => "checkbox",
                            "show_dependency" => [
                                "list/image",
                                "list/embed_url",
                                "list/image_file_url",
                            ]
                        ],
                        "affiliate" => [
                            "type" => "field",
                            "name" => "affiliate",
                            "label" => __("Affiliate Link", "zombify"),
                            "field_type" => "checkbox",
                            "show_dependency" => [
                                "list/image",
                                "list/embed_url",
                                "list/image_file_url",
                            ]
                        ],
                        "image_credit" => [
                            "type" => "field",
                            "name" => "image_credit",
                            "label" => __("Source URL", "zombify"),
                            "field_type" => "url",
                            "show_dependency" => "list/original_source",
                            "rules" => [
                                "url" => "1"
                            ],
                        ],
                        "image_credit_text" => [
                            "type" => "field",
                            "name" => "image_credit_text",
                            "label" => __("Credit", "zombify"),
                            "field_type" => "text",
                            "show_dependency" => "list/original_source"
                        ],
                        "embed_url" => [
                            "type" => "field",
                            "name" => "embed_url",
                            "label" => __("Embed / URL", "zombify"),
                            "field_type" => "textarea",
                        ],
                        "embed_thumb" => [
                            "type" => "field",
                            "name" => "embed_thumb",
                            "label" => __("Embed Thumbnail", "zombify"),
                            "field_type" => "hidden",
                            "use_as_featured" => true,
                        ],
                        "embed_type" => [
                            "type" => "field",
                            "name" => "embed_type",
                            "label" => __("Embed Type", "zombify"),
                            "field_type" => "hidden",
                        ],
                        "embed_variables" => [
                            "type" => "field",
                            "name" => "embed_variables",
                            "label" => __("Embed Variables", "zombify"),
                            "field_type" => "hidden",
                        ],
                        "mediatype" => [
                            "type" => "field",
                            "name" => "mediatype",
                            "label" => __("Media type", "zombify"),
                            "field_type" => "radio",
                        ],
                        "shop_url" => [
                            "type" => "field",
                            "name" => "shop_url",
                            "label" => __("Affiliate URL", "zombify"),
                            "field_type" => "url",
							"show_dependency" => "list/affiliate",
                            "rules" => [
                                "url" => "1"
                            ],
                        ],
                        "shop_button_text" => [
	                        "type" => "field",
	                        "name" => "shop_button_text",
	                        "label" => __("Affiliate button text", "zombify"),
	                        "field_type" => "text",
	                        "show_dependency" => "list/affiliate"
                        ],
                        "description" => [
                            "type" => "field",
                            "name" => "description",
                            "label" => __("Description", "zombify"),
                            "field_type" => "textarea",
                        ]
                    ]

                ]

            ];

        }

    }

}