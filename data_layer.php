<?php


class Wordpress_Data_Layer_Model {

    /**
     * Get the data layer and return it in json.
     *
     */
    public function get() {
        $data_layer = (object) array();
        $data_layer->site = $this->get_site_data();
        $data_layer->page = $this->get_page_data();
        $data_layer->user = $this->get_user_data();
        if (is_single()) {
            $data_layer->post = $this->get_post_data();
        } 
        return json_encode($data_layer);
    }

    /**
     * Get the site details for the data layer.
     *
     */
    public function get_site_data() {
        $site = (object) array(); 
        $site->name = get_bloginfo("name");
        $site->description = get_bloginfo("description");
        $site->wordpress_version = get_bloginfo("version");
        $site->language = get_bloginfo("language");
        return $site;
    }

    /**
     * Get the page details for the data layer.
     *
     */
    public function get_page_data() {
        $page = (object) array();
        $page->type = $this->get_page_type();
        $page->is_paged = is_paged();
        return $page;
    }

    /**
     * Get the site details for the data layer.
     *
     */
    public function get_page_type() {

        if (is_home()) {
            $type = "home";
        } else if (is_page()) {
            $type = "page";
        } else if (is_single()) {
            $type = get_post_type();
        } else if (is_category()) {
            $type = "category";
        } else if (is_tag()) {
            $type = "tag";
        } else if (is_tax()) {
            $type = "taxonomy"; 
        } else if (is_author()) {
            $type = "author";
        } else if (is_archive()) {
            $type = "archive";
        } else if (is_search()) {
            $type = "search";
        } else if (is_404()) {
            $type = "404";
        } else if (is_attachment()) {
            $type = "attachment";
        } else if (is_preview()) {
            $type = "preview_post";
        }

        return $type;
    }

    /**
     * Get the user details for the data layer.
     *
     */
    public function get_user_data() {
        $userData = wp_get_current_user();
        $user = (object) array();
        if ($userData->ID) {
            $user->id = $userData->ID;
            $user->name = $userData->display_name;
            $user->email = $userData->user_email;
            $user->registered_date = $userData->user_registered;
            $user->logged_in = true;
            $user->role = $userData->roles[0];
        } else {
            $user->logged_in = false;
        }
        $user->ip = $this->get_user_ip();
       
        return $user;
    }

    /**
     * Get the IP of a user
     *
     */
    public function get_user_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Get the post details for the data layer.
     *
     */
    public function get_post_data() {
        $post = (object) array();
        $postData = get_post();

        $post->id = $postData->ID;
        $post->title = $postData->post_title;
        $post->publish_date = $postData->post_date;
        $post->last_modified_date = $postData->post_modified;
        $post->url = get_permalink($post->id);
        $post->comment_count = $postData->comment_count;
        $post->type = $postData->post_type;
        $post->author_id = $postData->post_author;
        $post->author_name = get_the_author();
        $post->categories = $this->get_post_categories();
        $post->images = $this->get_post_images($post);

        return $post;
    }

    /**
     * Get the post categories for the post data in the data layer.
     *
     */
    public function get_post_categories() {
        $categoryData = get_the_category();
        $categories = array();
        foreach($categoryData as $item) {
            $category = (object) array();
            $category->name = $item->name;
            $category->id = $item->cat_ID;
            $category->slug = $item->slug;
            array_push($categories, $category);
        }
        return $categories;
    }

    /**
     * Get the post images for the post data in the data layer.
     *
     */
    public function get_post_images($post) {
        $imageData = get_children(array(
            "post_parent" => $post->id,
            "post_type" => "attachment"
        ));
        $images = array();
        foreach ($imageData as $item) {
            if (in_array($item->post_mime_type, array("image/png", "image/gif", "image/jpeg"))) {
                $image = (object) array();
                $image->id = $item->ID;
                $image->url = $item->guid;
                $image->title = $item->post_title;
                $image->publish_date = $item->post_date;
                $image->last_modified_date = $item->post_modified;
                array_push($images, $image);
            }
        }
        return $images;
    }

}