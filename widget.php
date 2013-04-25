<?php
/**
 * Adds Wikipedia Widget.
 */
class Wikipedia_Widget extends WP_Widget {

	protected static $options_default = array(
			'title' => 'Wikipedia Widget',
			'wikipedia_url' => 'http://en.wikipedia.org/',
			'limit' => '5',
			'search_term' => '',
			'search_form' => 'on',
			'search_field_alt' => '',
		);

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'wikipedia_widget',
			'Wikipedia Widget',
			array( 'description' => __( 'Shows wikipedia search results depending on a given string or the current post title.', 'wikipedia_widget' ), )
		);		
		//add ajax action
		add_action('wp_ajax_wikipedia_request', array($this, 'ajax_wikipedia_search_request') );
		add_action('wp_ajax_nopriv_wikipedia_request', array($this, 'ajax_wikipedia_search_request') );		
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
		extract( $instance );

		if ( is_single() ) {
			global $post;
			$search_term = ( $instance['search_term'] = trim(preg_replace("#[,|;|-|.|!|?|(].*#","", get_the_title($post) )) );
		}
		echo $before_widget;
		echo ! empty( $title ) ? $before_title . $title . $after_title : ''; ?>

		<form action="" method="" class="<?php echo $this->id_base; ?>-search_form">
			<?php 
			echo $search_form == 'on' ? '<input class="' . $this->id_base . '-search" type="text" placeholder="' . __('Search') . '" />' : ''; 
			echo $search_term ? '<input type="text" class="' . $this->id_base . '-default_search" value="' . $search_term . '" hidden />' : ''; ?>
			<div class="<?php echo $this->id_base; ?>-loader" style="display:none"><img src="<?php echo plugins_url('/loader.gif' , __FILE__ ); ?>" /></div>
			<input type="submit" value="Los" />
		</form>
		<div class="<?php echo $this->id_base; ?>-result"></div>
		<?php echo $after_widget;
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
		$instance = array_map("strip_tags", $new_instance);
		$instance['search_form'] = $instance['search_form'] ? $instance['search_form'] : 'off';
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
		$instance = ($instance !== false) ? array_merge(self::$options_default, $instance) : self::$options_default;
		extract($instance);
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input type="text" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'wikipedia_url' ); ?>"><?php _e( 'Wikipedia URL:' ); ?></label> 
			<input type="url" class="widefat" list="<?php echo $this->get_field_id( 'wikipedia_urls' ); ?>" id="<?php echo $this->get_field_id( 'wikipedia_url' ); ?>" name="<?php echo $this->get_field_name( 'wikipedia_url' ); ?>" id="<?php echo $this->get_field_id( 'wikipedia_url' ); ?>" value="<?php echo esc_attr( $wikipedia_url ); ?>" required />
			<datalist id="<?php echo $this->get_field_id( 'wikipedia_urls' ); ?>">
				<option value="http://de.wikipedia.org/">
				<option value="http://en.wikipedia.org/">
				<option value="http://fr.wikipedia.org/">
				<option value="http://it.wikipedia.org/">
				<option value="http://ru.wikipedia.org/">
			</datalist>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Search results max:' ); ?></label> 
			<input type="number" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo esc_attr( $limit ); ?>" size="3" id="<?php echo $this->get_field_id( 'limit' ); ?>" />
		</p>
		<p>
			<input <?php checked($search_form, 'on'); ?> class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'search_form' ); ?>" name="<?php echo $this->get_field_name( 'search_form' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'search_form' ); ?>"><?php _e( 'Show search form' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'search_term' ); ?>"><?php _e( 'Search at categories/pages:' ); ?></label>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name( 'search_term' ); ?>" id="<?php echo $this->get_field_id( 'search_term' ); ?>" value="<?php echo $search_term; ?>" placeholder="<?php _e('Default search string'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'search_field_alt' ); ?>"><?php _e( 'Alternative search form:' ); ?></label>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name( 'search_field_alt' ); ?>" id="<?php echo $this->get_field_id( 'search_field_alt' ); ?>" value="<?php echo $search_field_alt; ?>" placeholder="id or class" title="example: #my-input-form" />
		</p>	
	<?php }
	
	/**
	 * Return formatted wikipedia search-results 
	 *
	 * @param object $data 	XML Search result from wikipedia-request
	 *
	 * @return string search-results as html-list
	 */
	public function format_search_results( $data ) {		
		$xml_result = simplexml_load_string($data);
		$result = ! $xml_result->Section->Item ? sprintf( __('<li>No results. <a href="https://wikipedia.org/wiki/Special:Search?search=%s&go=Go" target="_blank">Try manually</a>.</li>'), urlencode($xml_result->Query) ) : "";
		foreach($xml_result->Section->Item as $data => $value) {	
			$result .= "<li>";
			$result .= ! isset($value->Image[0]['source']) ? '' : "<img src='" . $value->Image[0]['source'] . "' />";
			$result .= "<a href='" . $value->Url . "' target='_blank'>" . $value->Text . "</a> | " . $value->Description . "</li>";
		}
		return "<ul>" . $result . "</ul>";
	}

	/**
	 * Do a POST request to the wikipedia-API
	 *
	 * @param string $url 		Wikipedia URL
	 * @param string $search 	Search term
	 * @param string $limit 	Search results limit
	 *
	 * @return string search-results
	 */
	public function wikipedia_search_request($url, $search, $limit) {		
		global $wp_version;
		$data = array('format' => 'xml', 'action' => 'opensearch', 'search' => $search, 'limit' => $limit, 'namespace' => '0');
		$url = parse_url( $url . 'w/api.php' );
	    $host = $url['host'];
	    $path = $url['path'];

		if ( function_exists( 'wp_remote_post' ) ) {
			$http_args = array(
				'body'			=> $data,
				'method'		=> 'POST',
				'headers'		=> array(
					'Content-Type'	=> 'application/x-www-form-urlencoded; charset=' . get_option( 'blog_charset' ),
					'Host'			=> $host,
					'User-Agent'	=> 'User-Agent: WordPress/' . $wp_version . '; ' . get_bloginfo('url')
				),
				'httpversion'	=> '1.0',
				'timeout'		=> 15
			);
			$wikipedia_url = "http://{$host}{$path}";
			$response = wp_remote_post( $wikipedia_url, $http_args );
			if ( is_wp_error( $response ) ) {
				return sprintf( __('<p class="ww_error">Error while fetching search results: %s</p>'), $response->get_error_message() );
			} else {
				return $this->format_search_results($response['body']);
			}
		} else {
			return sprintf("<p class='ww_error'>Didn't find function wp_remote_post(). Are you using Wordpress < 2.1 ?).</p>");
		}	
	}

	/**
	 * Call and echo search request. either from wikipedia or from cache if exists.
	 * For the default search the cache will be created if not exist.
	 */
	public function ajax_wikipedia_search_request() {
		extract( $_POST );
		if ( empty($url) || empty($search) || empty($limit) ) {
			return sprintf("<p class='ww_error'>Error while getting POST-Parameters.</p>");
		}
		$default_search_term = array_shift(get_option('widget_wikipedia_widget'));		
		$content = ($search == $default_search_term['search_term']) ? html_entity_decode( get_transient( 'widget_wikipedia_widget_cache' ) ) : $this->wikipedia_search_request( $url, $search, $limit );
		if ( empty($content) ) { //perform default search-cache
			$re_check_time = 7 * 24 * 60 * 60;  // 1 week		
			$content = $this->wikipedia_search_request( $url, $search, $limit );
			set_transient( 'widget_wikipedia_widget_cache', htmlentities($content), $re_check_time );
		}
		echo $content;
		die(); //required for ajax call
	}

} // class Wikipedia_Widget
