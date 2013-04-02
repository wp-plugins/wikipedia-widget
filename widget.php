<?php
/**
 *
 * Adds Wikipedia Widget.
 */
class Wikipedia_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'wikipedia_widget', // Base ID
			'Wikipedia Widget', // Name
			array( 'description' => __( 'Shows wikipedia search results depending on a given string or the current post title.', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$url = $instance['wikipedia_url']; 
		$limit = $instance['limit'];
		$search_form = $instance['search_form'];
		$search_term = $instance['search_term'];
		$form_header = "";

		if ( $search_form ) {
			$form_header .= '<input class="' . $this->id_base . '-search" type="text" placeholder="Wikipedia durchsuchen" />';
		}

		if ( is_single() ) {
			global $post;
			$search_term = get_the_title($post);
		}
		if ( $search_term ) {
			$form_header .= '<input class="' . $this->id_base . '-search_now" value="' . $search_term . '" type="hidden" />';
		}

		echo $before_widget;

		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		?>			
		<form action="" method="" class="<?php echo $this->id_base; ?>-search_form">
			<?php echo $form_header; ?>
			<div class="<?php echo $this->id_base; ?>-loader" style="display:none"><img src="<?php echo plugins_url('/ajax-loader.gif' , __FILE__ ); ?>" /></div>
			<input type="hidden" value="<?php echo $url; ?>" class="<?php echo $this->id_base; ?>-url" />
			<input type="hidden" value="<?php echo $limit; ?>" class="<?php echo $this->id_base; ?>-limit" />
			<input type="submit" value="Los" />
		</form>
		<div class="<?php echo $this->id_base; ?>-result"></div>
		<?php 
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['wikipedia_url'] = strip_tags( $new_instance['wikipedia_url'] );
		$instance['limit'] = strip_tags( $new_instance['limit'] );
		$instance['search_term'] = strip_tags( $new_instance['search_term'] );
		$instance['search_form'] = strip_tags( $new_instance['search_form'] );
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = isset($instance['title']) ? $instance['title'] : 'Wikipedia Widget';
		$wikipedia_url = isset($instance['wikipedia_url']) ? $instance['wikipedia_url'] : 'http://de.wikipedia.org/';
		$limit = isset($instance['limit']) ? $instance['limit'] : '5';
		$search_term = isset($instance['search_term']) ? $instance['search_term'] : '';
		$search_form = isset($instance['search_form']) ? $instance['search_form'] : true;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Titel:' ); ?></label> 
			<input type="text" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'wikipedia_url' ); ?>"><?php _e( 'Wikipedia URL:' ); ?></label> 
			<input type="url" class="widefat" list="<?php echo $this->get_field_id( 'wikipedia_urls' ); ?>" id="<?php echo $this->get_field_id( 'wikipedia_url' ); ?>" name="<?php echo $this->get_field_name( 'wikipedia_url' ); ?>" id="<?php echo $this->get_field_id( 'wikipedia_url' ); ?>" value="<?php echo esc_attr( $wikipedia_url ); ?>" required />
			<datalist id="<?php echo $this->get_field_id( 'wikipedia_urls' ); ?>">
				<option value="http://de.wikipedia.org/">
				<option value="http://en.wikipedia.org/">
				<option value="http://fr.wikipedia.org/">
				<option value="http://ru.wikipedia.org/">
			</datalist>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Anzahl Suchergebnisse max:' ); ?></label> 
			<input type="number" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo esc_attr( $limit ); ?>" size="3" id="<?php echo $this->get_field_id( 'limit' ); ?>" />
		</p>
		<p>
			<input <?php if ($search_form) echo "checked"; ?> class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'search_form' ); ?>" name="<?php echo $this->get_field_name( 'search_form' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'search_form' ); ?>"><?php _e( 'Zeige Suchfeld:' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'search_term' ); ?>"><?php _e( 'Suche bei Kategorien/Blog-Seiten:' ); ?></label>
			<input type="text" class="widefat <?php echo $this->id_base; ?>-search_term" name="<?php echo $this->get_field_name( 'search_term' ); ?>" id="<?php echo $this->get_field_id( 'search_term' ); ?>" value="<?php echo $search_term; ?>" placeholder="Default Wikipedia search string" title="Default Wikipedia search string" />
		</p>	
	<?php }

} // class Wikipedia_Widget