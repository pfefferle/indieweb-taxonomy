<?php
// Adds Post Meta Box for Kind Taxonomy
// Plan is to optionally automate filling in of this data from secondary plugins


// Add meta box to new post/post pages only 
add_action('load-post.php', 'kindbox_setup');
add_action('load-post-new.php', 'kindbox_setup');

/* Meta box setup function. */
function kindbox_setup() {

  /* Add meta boxes on the 'add_meta_boxes' hook. */
  add_action( 'add_meta_boxes', 'kindbox_add_postmeta_boxes' );
}

/* Create one or more meta boxes to be displayed on the post editor screen. */
function kindbox_add_postmeta_boxes() {

  add_meta_box(
    'responsebox-meta',      // Unique ID
    esc_html__( 'In Response To', 'kind_taxonomy' ),    // Title
    'response_metabox',   // Callback function
    'post',         // Admin page (or post type)
    'normal',         // Context
    'default'         // Priority
  );

  add_meta_box(
    'locationbox-meta',      // Unique ID
    esc_html__( 'Location', 'kind_taxonomy' ),    // Title
    'location_metabox',   // Callback function
    'post',         // Admin page (or post type)
    'normal',         // Context
    'default'         // Priority
  );


}

function response_metabox( $object, $box ) { ?>

  <?php wp_nonce_field( 'response_metabox', 'response_metabox_nonce' ); ?>

  <p>
    <label for="response_url"><?php _e( "URL", 'kind_taxonomy' ); ?></label>
    <br />
    <input type="text" name="response_url" id="response_url" value="<?php echo esc_attr( get_post_meta( $object->ID, 'response_url', true ) ); ?>" size="70" />
    <br />
    <label for="response_title"><?php _e( "Custom Title", 'kind_taxonomy' ); ?></label>
    <br />
    <input type="text" name="response_title" id="response_title" value="<?php echo esc_attr( get_post_meta( $object->ID, 'response_title', true ) ); ?>" size="70" />
	<br />
    <label for="response_quote"><?php _e( "Citation", 'kind_taxonomy' ); ?></label>
    <br />
    <textarea name="response_quote" id="response_quote" cols="70"><?php echo esc_attr( get_post_meta( $object->ID, 'response_quote', true ) ); ?></textarea>
  
  </p>

<?php }

function location_metabox( $object, $box ) { ?>

  <?php wp_nonce_field( 'location_metabox', 'location_metabox_nonce' ); ?>
   <script language="javascript">
	function getLocation()
  	   {
  		if (navigator.geolocation)
			{
		      navigator.geolocation.getCurrentPosition(showPosition);
	   }
  		else{alert("Geolocation is not supported by this browser.");}
  }
function showPosition(position)
  {
	document.getElementById("geo_latitude").value = position.coords.latitude;
     	document.getElementById("geo_longitude").value = position.coords.longitude;

  }
  </script>


  <p>
    <label for="geo_public"><?php _e( "Public", 'kind_taxonomy' ); ?></label>
    <input type="checkbox" name="geo_public" id="geo_public" value="<?php echo esc_attr( get_post_meta( $object->ID, 'geo_public', true ) ); ?>" />
    <br />
    <label for="geo_latitude"><?php _e( "Latitude", 'kind_taxonomy' ); ?></label>
    <input type="text" name="geo_latitude" id="geo_latitude" value="<?php echo esc_attr( get_post_meta( $object->ID, 'geo_latitude', true ) ); ?>" size="30" />
    <br />
    <label for="geo_longitude"><?php _e( "Longitude", 'kind_taxonomy' ); ?></label>
    <input type="text" name="geo_longitude" id="geo_longitude" value="<?php echo esc_attr( get_post_meta( $object->ID, 'geo_longitude', true ) ); ?>" size="30" />
    <br />
    <label for="geo_address"><?php _e( "Human-Readable Address (Optional)", 'kind_taxonomy' ); ?></label>
    <br />
    <input type="text" name="geo_address" id="geo_address" value="<?php echo esc_attr( get_post_meta( $object->ID, 'geo_address', true ) ); ?>" size="70" /  
    <span class="mapp" onmouseover="getLocation()">Retrieve Location</span>
 </p>

<?php }


/* Save the meta box's post metadata. */
function responsebox_save_post_meta( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['response_metabox_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['response_metabox_nonce'], 'response_metabox' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, its safe for us to save the data now. */
	if( isset( $_POST[ 'response_url' ] ) ) {
        update_post_meta( $post_id, 'response_url', esc_url_raw( $_POST[ 'response_url' ] ) );
	}
	if( isset( $_POST[ 'response_title' ] ) ) {
        update_post_meta( $post_id, 'response_title', esc_attr( $_POST[ 'response_title' ] ) );
    }
	if( isset( $_POST[ 'response_quote' ] ) ) {
        update_post_meta( $post_id, 'response_quote', esc_attr( $_POST[ 'response_quote' ] ) );
    }

}

add_action( 'save_post', 'responsebox_save_post_meta' );

/* Save the meta box's post metadata. */
function locationbox_save_post_meta( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['location_metabox_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['location_metabox_nonce'], 'location_metabox' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, its safe for us to save the data now. */
	if( isset( $_POST[ 'geo_latitude' ] ) ) {
        update_post_meta( $post_id, 'geo_latitude', esc_attr( $_POST[ 'geo_latitude' ] ) );
	}
	if( isset( $_POST[ 'geo_longitude' ] ) ) {
        update_post_meta( $post_id, 'geo_longitude', esc_attr( $_POST[ 'geo_longitude' ] ) );
    }
	if( isset( $_POST[ 'geo_address' ] ) ) {
        update_post_meta( $post_id, 'geo_address', esc_attr( $_POST[ 'geo_address' ] ) );
    }
	if( isset( $_POST[ 'geo_public' ] ) ) {
        update_post_meta( $post_id, 'geo_public', esc_attr( $_POST[ 'geo_public' ] ) );
    }

}

add_action( 'save_post', 'locationbox_save_post_meta' );


add_action( 'add_meta_boxes', 'make_wp_editor_movable', 0 );
	function make_wp_editor_movable() {
		global $_wp_post_type_features;
		if (isset($_wp_post_type_features['post']['editor']) && $_wp_post_type_features['post']['editor']) {
			unset($_wp_post_type_features['post']['editor']);
			add_meta_box(
				'content_sectionid',
				__('Content'),
				'movable_inner_custom_box',
				'post', 'normal', 'high'
			);
		}
	}
	function movable_inner_custom_box( $post ) {
		the_editor($post->post_content);
	}


?>

