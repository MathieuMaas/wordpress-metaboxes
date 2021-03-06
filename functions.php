/**
 * Generated by the WordPress Meta Box Generator at http://goo.gl/8nwllb
 */
class Rational_Meta_Box {
	private $screens = array(
		'swim_games',
	);
	private $fields = array(
		array(
			'id' => 'afbeelding-sequence',
			'label' => 'Afbeelding Sequence',
			'type' => 'multimedia',
		),
		array(
			'id' => 'totale-lengte-afbeelding',
			'label' => 'Totale lengte afbeelding',
			'type' => 'number',
		),
		array(
			'id' => 'totale-breedte-afbeelding',
			'label' => 'Totale breedte afbeelding',
			'type' => 'number',
		),
		array(
			'id' => 'aantal-stappen-in-animatie',
			'label' => 'Aantal stappen in animatie',
			'type' => 'number',
		),
		array(
			'id' => 'achtergrond-afbeelding',
			'label' => 'Achtergrond afbeelding',
			'type' => 'media',
		),
		array(
			'id' => 'logo-van-game',
			'label' => 'Logo van Game',
			'type' => 'media',
		),
		array(
			'id' => 'content-van-game',
			'label' => 'Content van Game',
			'type' => 'textarea',
		),
		array(
			'id' => 'youtube-video-url',
			'label' => 'Youtube video URL',
			'type' => 'url',
		),		
	);

	/**
	 * Class construct method. Adds actions to their respective WordPress hooks.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
	}

	/**
	 * Hooks into WordPress' add_meta_boxes function.
	 * Goes through screens (post types) and adds the meta box.
	 */
	public function add_meta_boxes() {
		remove_meta_box( 'slugdiv', 'post', 'normal'  );
		foreach ( $this->screens as $screen ) {
			add_meta_box(
				'custom-swim-game',
				__( 'Custom Swim Game', 'rational-metabox' ),
				array( $this, 'add_meta_box_callback' ),
				$screen,
				'advanced',
				'default'
			);
		}
	}

	/**
	 * Generates the HTML for the meta box
	 * 
	 * @param object $post WordPress post object
	 */
	public function add_meta_box_callback( $post ) {
		wp_nonce_field( 'custom_swim_game_data', 'custom_swim_game_nonce' );
		$this->generate_fields( $post );
	}

	/**
	 * Hooks into WordPress' admin_footer function.
	 * Adds scripts for media uploader.
	 */
	public function admin_footer() {
		?><script>			
			jQuery(document).ready(function($){
				if ( typeof wp.media !== 'undefined' ) {
					var _custom_media = true,
					_orig_send_attachment = wp.media.editor.send.attachment;
					$('.rational-metabox-media').click(function(e) {
						var send_attachment_bkp = wp.media.editor.send.attachment;
						var button = $(this);
						var id = button.attr('id').replace('_button', '');
						_custom_media = true;
							wp.media.editor.send.attachment = function(props, attachment){
							if ( _custom_media ) {
								$("#"+id).val(attachment.url);
							} else {
								return _orig_send_attachment.apply( this, [props, attachment] );
							};
						}
						wp.media.editor.open(button);
						return false;
					});
					$('.add_media').on('click', function(){
						_custom_media = false;
					});
				}			
		    var custom_uploader;
		    $('.rational-metabox-multimedia').click(function(e) {
			    var button = $(this);
				var id = button.attr('id').replace('_button', '');
		        e.preventDefault();
		        //If the uploader object has already been created, reopen the dialog
		        if (custom_uploader) {
		            custom_uploader.open();
		        }
		        //Extend the wp.media object
		        custom_uploader = wp.media.frames.file_frame = wp.media({
		            title:"Choose multiple Images",
		            button: {
		                text:"Choose images"
		            },
		            multiple: true
		        });
		        custom_uploader.on('select', function() {
		            var selection = custom_uploader.state().get('selection');
		            $("#"+id).val('');
		            $(".imgbox_"+id).html('');
		            selection.map( function( attachment ) {
		            	attachment = attachment.toJSON();		               			               
						$("#"+id).val(function(i,val) { 
						     return val + (!val ? '' : ', ') + attachment.url;
						});						                
						var curID = button.attr('id');						
		                $(".imgbox_"+id).append("<img style='background-color:#eee; margin-right:15px; margin-top:15px;' width='200' src=" +attachment.url+">");                    
		            });
		        });
		        custom_uploader.open();
		    });
		});
		</script><?php
	}

	/**
	 * Generates the field's HTML for the meta box.
	 */
	public function generate_fields( $post ) {
		$output = '';
		foreach ( $this->fields as $field ) {
			$label = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
			$db_value = get_post_meta( $post->ID, 'custom_swim_game_' . $field['id'], true );
			switch ( $field['type'] ) {
				case 'media':
					$input = sprintf(
						'<input class="regular-text" id="%s" name="%s" type="text" value="%s"> <input class="button rational-metabox-media" id="%s_button" name="%s_button" type="button" value="Upload" /></div>',
						$field['id'],
						$field['id'],
						$db_value,
						$field['id'],
						$field['id']						
					);
					break;
				case 'multimedia':					
					$imgcontent = "";				
					$var=explode(',',$db_value);
					foreach($var as $row){
					   $imgcontent .= "<img style='background-color:#eee; margin-right:15px; margin-top:15px;' width='200' src='".$row."'>";
				    }										
					$input = sprintf(
						'<input class="regular-text" id="%s" name="%s" type="hidden" value="%s"> <input class="button rational-metabox-multimedia" id="%s_button" name="%s_button" type="button" value="Select Images" /><div class="imgbox_%s">%s</div>',
						$field['id'],
						$field['id'],
						$db_value,
						$field['id'],
						$field['id'],
						$field['id'],
						$imgcontent
					);
					break;
				case 'textarea':
					$input = sprintf(
						'<textarea class="large-text" id="%s" name="%s" rows="5">%s</textarea>',
						$field['id'],
						$field['id'],
						$db_value
					);
					break;
				default:
					$input = sprintf(
						'<input %s id="%s" name="%s" type="%s" value="%s">',
						$field['type'] !== 'color' ? 'class="regular-text"' : '',
						$field['id'],
						$field['id'],
						$field['type'],
						$db_value
					);
			}
			$output .= $this->row_format( $label, $input );
		}
		echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
	}

	/**
	 * Generates the HTML for table rows.
	 */
	public function row_format( $label, $input ) {
		return sprintf(
			'<tr><th scope="row">%s</th><td>%s</td></tr>',
			$label,
			$input
		);
	}
	/**
	 * Hooks into WordPress' save_post function
	 */
	public function save_post( $post_id ) {
		if ( ! isset( $_POST['custom_swim_game_nonce'] ) )
			return $post_id;

		$nonce = $_POST['custom_swim_game_nonce'];
		if ( !wp_verify_nonce( $nonce, 'custom_swim_game_data' ) )
			return $post_id;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		foreach ( $this->fields as $field ) {
			if ( isset( $_POST[ $field['id'] ] ) ) {
				switch ( $field['type'] ) {
					case 'email':
						$_POST[ $field['id'] ] = sanitize_email( $_POST[ $field['id'] ] );
						break;
					case 'text':
						$_POST[ $field['id'] ] = sanitize_text_field( $_POST[ $field['id'] ] );
						break;
				}
				update_post_meta( $post_id, 'custom_swim_game_' . $field['id'], $_POST[ $field['id'] ] );
			} else if ( $field['type'] === 'checkbox' ) {
				update_post_meta( $post_id, 'custom_swim_game_' . $field['id'], '0' );
			}
		}
	}
}
new Rational_Meta_Box;
